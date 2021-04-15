<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;
use JustSwap\Trade;

if(count($argv) < 2) {
  die('usage: php swap-exact-trx-for-tokens.php <hub|wiz>' . PHP_EOL);
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

echo 'swap exact trx for tokens...' . PHP_EOL;

$trade = $kit->getTradeBuilder()
             ->tradeType(Trade::EXACT_INPUT)
             ->tokenIn(SwapKit::TRX_TOKEN)
             ->TokenOut($tokenAddr)
             ->amountIn(bn('100000'))
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
