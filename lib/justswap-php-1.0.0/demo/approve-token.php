<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\SwapKit;

$kit = new SwapKit(
  TronApi::testNet(),
  Credential::fromPrivateKey(ALICE_PRIVKEY),
  FACTORY_ADDRESS
);

//check allowance and approve if necessary
$tokens = [HUB_ADDRESS, WIZ_ADDRESS];

foreach($tokens as $addr){
  
  $token = $kit->getToken($addr);
  $id = $token->symbol();

  echo '>>process token ' . $id . '...' . PHP_EOL;

  $exchangeAddr = $kit->getFactory()->getExchange($addr);
  if($exchangeAddr == SwapKit::ZERO_ADDRESS){
    die('exchange not exists: ' . $id . PHP_EOL);
  }
  echo 'exchange address => ' . $exchangeAddr . PHP_EOL;
  
  $allowance = $token->allowance($kit->getDefaultAddress(), $exchangeAddr);
  if($allowance->compare(bn(0)) == 0 ){
    echo  'approve now...' . PHP_EOL;
    $txid = $token->approve($exchangeAddr, SwapKit::I256_UNLIMITED, []);
    echo 'txid => ' . $txid . PHP_EOL;
    echo 'waiting for confirmation...' . PHP_EOL;
    $success = $kit->waitForConfirmation($txid);
    echo 'success => ' . $success . PHP_EOL;
  }else{
    echo 'allowance: ' . $allowance . PHP_EOL;
  }
}
