<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'vendor/autoload.php';
require 'lib/Address.php';
require 'lib/TronTrade.php';

$instance = TronTrade::getInstance();

while (true) {
    try {
        $init = $instance->init();
    } catch (Exception $e) {
        echo $e, PHP_EOL;
        sleep(12);
        continue;
    }
    sleep(10);
}


