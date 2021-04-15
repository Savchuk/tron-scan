<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;
use JustSwap\Order;

if(count($argv) < 2) {
  die('usage: php add-liquidity.php <hub|wiz>' . PHP_EOL);
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

if(!$kit->hasExchangeForToken($tokenAddr)) {   
  die('exchange not exists.' . PHP_EOL);
}

if($kit->isExchangeEmptyForToken($tokenAddr)) {   
  echo 'exchange has no liquidity, add now...' . PHP_EOL;
  $order = $kit->getOrderBuilder()
               ->orderType(Order::LIQUIDITY_ADD)
               ->tokenA(SwapKit::TRX_TOKEN)
               ->tokenB($tokenAddr)
               ->amountA(bn('10000000'))  //at least 10 trx is required 
               ->amountB(bn('10000000000000000000')) // 10 tokens
               ->build();
  echo '>>order info...' . PHP_EOL;
  echo $order . PHP_EOL;
  
  echo '>>execute order...' . PHP_EOL;
  $txid = $kit->executeOrder($order);
  echo 'txid => ' . $txid . PHP_EOL;
  
  echo '>>waiting for confirmation...' . PHP_EOL;
  $success = $kit->waitForConfirmation($txid);
  echo 'success => ' . $success . PHP_EOL;
  
}else {
  echo 'exchange has some liquidity, add more...' . PHP_EOL;
  
  $info = $kit->getExchangeLiquidityPositionForToken($kit->getDefaultAddress(), $tokenAddr);
  echo '>>position info...' . PHP_EOL;
  echo 'LP total supply => ' . $info->totalSupply . PHP_EOL;
  echo 'LP balance => ' . $info->balance . PHP_EOL;
  echo 'LP share => ' . $info->share . '%' . PHP_EOL;

  $order = $kit->getOrderBuilder()
               ->orderType(Order::LIQUIDITY_ADD)
               ->tokenA(SwapKit::TRX_TOKEN)
               ->tokenB($tokenAddr)
               ->amountA(bn('100000'))
               ->build();
  echo '>>order info...' . PHP_EOL;
  echo $order . PHP_EOL;

  echo '>>execute order...' . PHP_EOL;  
  $txid = $kit->executeOrder($order);
  echo 'txid => ' . $txid . PHP_EOL;
  
  echo '>>waiting for confirmation...' . PHP_EOL;
  $success = $kit->waitForConfirmation($txid);
  echo 'success => ' . $success . PHP_EOL;
}
