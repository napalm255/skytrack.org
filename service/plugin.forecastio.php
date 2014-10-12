<?php

  include('httpful.phar');
  include('weather.class.php');

  $plugin = new StdClass();
  $plugin->name = "forecastio";

  $settings = new StdClass();
  $settings = json_decode(file_get_contents('settings.json'), false);

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
    $hourly = $res[$location->state][$location->city]->body->hourly->data;
    foreach ($hourly as $hour) {
      $name = '{ "plugin" : "'. $plugin->name . '", "state" : "'. $location->state .'", "city" : "'. $location->city .'" }';
      $recorded = time();
      $epoch = $hour->time;
      $weather_temp = "$hour->temperature";
      $weather_pop = $hour->precipProbability * 100;
      $weather_wspd = "$hour->windSpeed";
      $weather_windchill = '-9999';

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

echo "done.";
