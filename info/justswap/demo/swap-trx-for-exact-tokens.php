<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;
use JustSwap\Trade;

if(count($argv) < 2) {
  die('usage: php swap-trx-for-exact-tokens.php <hub|wiz>' . PHP_EOL);
}

$tokenAddr = $argv[1] == 'hub' ? HUB_ADDRESS : (
               $argv[1] == 'wiz' ? WIZ_ADDRESS : 
               die('supported token: hub | wiz'. PHP_EOL) 
             );
             
$kit = new SwapKit(
  TronApi::testNet(),
  Credential::fromPrivateKey(ALICE_PRIVKEY),
  FACTORY_ADDRESS
);

$token = $kit->getToken($tokenAddr);
echo  'token => ' .  $token->symbol() . PHP_EOL;

echo 'swap trx for exact tokens...' . PHP_EOL;

$trade = $kit->getTradeBuilder()
             ->tradeType(Trade::EXACT_OUTPUT)
             ->tokenIn(SwapKit::TRX_TOKEN)
             ->tokenOut($tokenAddr)
             ->amountOut(bn('100000000000000000'))
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