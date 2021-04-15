<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;
use JustSwap\Trade;

$factory_address = 'TXk8rQSAvPvBBNtqSoY6nCfsXWCSSpTVQF';
$key = '38ecbc853bea64ca46a825bb7e67b6edc4b779756f45fb3637c9434d2f1e0e6c';

$kit = new SwapKit(
  TronApi::mainNet(),
  Credential::fromPrivateKey($key),
  $factory_address 
);


$tokenIn = 'TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9'; //$tokenBtc
$tokenOut = 'THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF'; //$tokenEth
$amountIn = 221;
echo 'swap exact tokens for tokens...' . PHP_EOL;

//exit;

$trade = $kit->getTradeBuilder()
             ->tradeType(Trade::EXACT_INPUT)
             ->tokenIn($tokenIn)
             ->tokenOut($tokenOut)
             ->amountIn(bn($amountIn))
             ->to($kit->getDefaultAddress())
             ->build();
             
             
echo '>>trade info...' . PHP_EOL;
echo $trade . PHP_EOL; 

echo '>>execute trade...' . PHP_EOL;           
$txid = $kit->executeTrade($trade);             
echo 'txid => ' . $txid . PHP_EOL;

echo '>>waiting for confirmation...' . PHP_EOL;
$success = $kit->waitForConfirmation($txid);
echo 'success => ' . $success . PHP_EOL;
