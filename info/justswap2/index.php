<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'vendor/autoload.php';

use JustSwap\SwapKit;
use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\Trade;

        $kit = new SwapKit(
            TronApi::mainNet(),
            Credential::fromPrivateKey('38ecbc853bea64ca46a825bb7e67b6edc4b779756f45fb3637c9434d2f1e0e6c'),
            'TXk8rQSAvPvBBNtqSoY6nCfsXWCSSpTVQF'
        );

$balance = (int)$kit->getToken('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t')->call('balanceOf', 'TFvPSKtX7VtNbLuj8mXp962vxnpSiDJ1u6')->value;

echo $balance;

//require 'lib/TronTrade.php';

//$instance = TronTrade::getInstance();

//$init = $instance->init();


