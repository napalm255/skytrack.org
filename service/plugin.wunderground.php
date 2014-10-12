<?php

  include(dirname(__FILE__) . '/httpful.phar');
  include(dirname(__FILE__) . '/weather.class.php');

  $plugin = new StdClass();
  $plugin->name = "wunderground";

  $settings = new StdClass();
  $settings = json_decode(file_get_contents(dirname(__FILE__) . '/settings.json'), false);

  $plugin->apikey = $settings->{$plugin->name}->apikey;
  $plugin->url = $settings->{$plugin->name}->url;

  $res = array();

  foreach ($settings->locations as $location) {
    $find = array('/#APIKEY#/', '/#COORDS#/');
    $repl = array($plugin->apikey, $location->coords);
    $url = preg_replace($find, $repl, $plugin->url);
    $res[$location->state][$location->city] = \Httpful\Request::get($url)->send(); 
  }

  foreach ($settings->locations as $location) {
    foreach ($res[$location->state][$location->city]->body->hourly_forecast as $hour) {
      $name = '{ "plugin" : "'. $plugin->name .'", "state" : "'. $location->state .'", "city" : "'. $location->city .'" }';
      $recorded = time();
      $epoch = $hour->FCTTIME->epoch;
      $weather_temp = $hour->temp->english;
      $weather_pop = $hour->pop;
      $weather_wspd = $hour->wspd->english;
      $weather_windchill = $hour->windchill->english;

      $dyno = new Weather();
      try { $dyno->hourlyAdd(
        $name,
        $recorded,
        $epoch,
        $weather_temp,
        $weather_pop,
        $weather_wspd,
        $weather_windchill
      ); } catch (Exception $e) { print_r("Exception $e\n"); }
    }
  }

echo $plugin->name . " updated.";
