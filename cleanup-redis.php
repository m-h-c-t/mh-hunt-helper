<?php
require __DIR__ . '/vendor/autoload.php';

$redis = new Predis\Client();

deleteall();

// delete all keys in redis that are old
function deleteall() {
  global $redis;

  $list = $redis->keys("*");
  echo "Number of redis keys: " . sizeof($list) . "\n";
  echo "Deleting old redis keys:\n";

  foreach ($list as $key)
  {
//    echo "KEY: $key --- " . $redis->get($key) . "\n";
    if (abs(time() - $redis->get($key)) > 80) {
      echo "deleting key: $key \n" ;
      $redis->del($key);
    }
  }
  echo "Finished deleting all old redis keys\n";
}

