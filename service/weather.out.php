<?php

  include(dirname(__FILE__) . '/httpful.phar');
  include(dirname(__FILE__) . '/weather.class.php');

  $dyno = new Weather();

  #$dt = new DateTime('12 Oct 2014 12:00 AM');
  $dt = new DateTime(date('j M Y h:00 A', time()));
  $stamp = $dt->format('U');
  $date_start = date('l, F j Y @ h A', $stamp);

  $city = 'frederick';
  $state = 'maryland';
  $src = array('forecastio', 'wunderground');

  $out = new stdClass();
  $out->city = ucwords($city);
  $out->state = ucwords($state);

  $out->temp = new stdClass();
  $out->temp->hourly = array();
  $out->temp->categories = array();
  $out->pop = new stdClass();
  $out->pop->hourly = array();
  $out->pop->categories = array();

  $y = 0;
  foreach ($src as $val) {
    $out->temp->hourly[$y] = new stdClass();
    $out->temp->hourly[$y]->name = $val;
    $out->temp->hourly[$y]->data = array();
    $out->pop->hourly[$y] = new stdClass();
    $out->pop->hourly[$y]->name = $val;
    $out->pop->hourly[$y]->data = array();

    $stamp_inc = $stamp;
    for ($x = 0; $x <= 36; $x++) {
      $res = $dyno->hourlyRead('{ "plugin" : "' . $val . '", "state" : "' . $state . '", "city" : "' . $city . '" }', $stamp_inc);
      $out->pop->hourly[$y]->data[] = array(date('r', $stamp_inc), intval($res['weather_pop']['N']));
      $out->pop->categories[] = date('h A', $stamp_inc);
      $out->temp->hourly[$y]->data[] = array(date('r', $stamp_inc), intval($res['weather_temp']['N']));
      $out->temp->categories[] = date('h A', $stamp_inc);
      $out->date = $date_start . " to " . date('l, F j Y @ h A', $stamp_inc);
      $stamp_inc = $stamp_inc + 3600;
    }
    $y++;
  }

  echo json_encode($out);
