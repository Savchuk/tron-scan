<?php

require('./config.php');

use JustSwap\SwapKit;
use JustSwap\TronApi;
use JustSwap\Credential;

$api = TronApi::testNet();
$alice = Credential::fromPrivateKey(ALICE_PRIVKEY);
$kit = new SwapKit($api, $alice, FACTORY_ADDRESS);

$tokens = [HUB_ADDRESS, WIZ_ADDRESS];

foreach($tokens as $tokenAddr){
  $token = $kit->getToken($tokenAddr);
  $symbol = $token->symbol();
  
  echo '>>create exchange for token ' . $symbol . '...' . PHP_EOL;
  $txid = $kit->getFactory()->createExchange($tokenAddr, []);
  echo 'txid => ' . $txid . PHP_EOL;
  
  echo '>>waiting for confirmation...' . PHP_EOL;
  $success = $kit->waitForConfirmation($txid);
  echo 'success => ' . $success . PHP_EOL;
}