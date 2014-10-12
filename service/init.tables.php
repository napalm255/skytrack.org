<?php

  include('httpful.phar');
  include('weather.class.php');

  $dyno = new Weather();
  try { $dyno->table_create_users(); } catch (Exception $e) { print_r("Exception $e\n"); }
  try { $dyno->table_create_hourly(); } catch (Exception $e) { print_r("Exception $e\n"); }

echo "done.";
