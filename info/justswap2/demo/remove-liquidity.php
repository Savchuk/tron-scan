<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;
use JustSwap\Order;

if(count($argv) < 2) {
  die('usage: php remove-liquidity.php <hub|wiz>' . PHP_EOL);
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

echo 'remove liquidity...' . PHP_EOL;

echo '>>LP info...' . PHP_EOL;
$info = $kit->getExchangeLiquidityPositionForToken($kit->getDefaultAddress(), $tokenAddr);
echo 'LP total supply => ' . $info->totalSupply . PHP_EOL;
echo 'LP balance => ' . $info->balance . PHP_EOL;
echo 'LP share => ' . $info->share . '%' . PHP_EOL;

echo '>>remove 25% LP...' . PHP_EOL;
list($liquidity, $r) = $info->balance->divide(bn(4));
echo 'liquidity to remove => ' . $liquidity . PHP_EOL;

$order = $kit->getOrderBuilder()
             ->orderType(Order::LIQUIDITY_REMOVE)
             ->tokenA(SwapKit::TRX_TOKEN)
             ->tokenB($tokenAddr)
             ->liquidity($liquidity)
             ->build();
echo '>>order info...' . PHP_EOL;
echo $order . PHP_EOL;

echo '>>execute order...' . PHP_EOL;
$txid = $kit->executeOrder($order);             
echo 'txid => ' . $txid . PHP_EOL;

echo '>>waiting for confirmation...' . PHP_EOL;
$success = $kit->waitForConfirmation($txid);
echo 'success => ' . $success . PHP_EOL;
