<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;
use JustSwap\Trade;

$kit = new SwapKit(
  TronApi::testNet(),
  Credential::fromPrivateKey(ALICE_PRIVKEY),
  FACTORY_ADDRESS
);

$tokenIn = HUB_ADDRESS;
$tokenOut = WIZ_ADDRESS;

echo 'swap exact tokens for tokens...' . PHP_EOL;

$trade = $kit->getTradeBuilder()
             ->tradeType(Trade::EXACT_INPUT)
             ->tokenIn($tokenIn)
             ->tokenOut($tokenOut)
             ->amountIn(bn('1000000000000000000'))
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
