<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'vendor/autoload.php';
require 'lib/Address.php';
require 'lib/TronTrade.php';

$instance = TronTrade::getInstance();

    while (true) {
        try {
            $balance = $instance->balanceTrx();
            echo $balance, PHP_EOL;
        } catch (Exception $e) {
            echo 'ERROR', PHP_EOL;
            sleep(10);
            continue;
        }
        sleep(3);
    }
