<?php
require('./config.php');

use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\Contract;
use JustSwap\Address;

$api = TronApi::testNet();
$alice = Credential::fromPrivateKey(ALICE_PRIVKEY);
echo 'initiator => ' . $alice->address()->base58() . PHP_EOL;

function waitForTransactionInfo($txid, $tries = 60){
  global $api;

  for($i=0; $i<$tries; $i++){
    $info = $api->getTransactionInfo($txid);
    if(isset($info->receipt)) {
      return $info;
    }
    sleep(3); //idle for 3 seconds
  }
  
  throw new Exception('failed to get transaction info');
}

function deploy_contract($cname){
  global $api, $alice;
  
  $args = func_get_args();
  array_shift($args);
  
  echo '>>deploy contract ' . $cname . '...' . PHP_EOL;

  echo 'load contract abi and bytecode...' . PHP_EOL;
  $fnAbi = sprintf('../contracts/build/%s.abi', $cname);
  $fnBin = sprintf('../contracts/build/%s.bin', $cname);
  $abi = file_get_contents($fnAbi);
  $bytecode = file_get_contents($fnBin);

  $inst = new Contract($api, $abi, $alice);
  $inst->bytecode($bytecode);
  $txid = $inst->deploy($args);
  echo 'txid => ' . $txid . PHP_EOL;
  
  echo 'waiting for transaction info...' . PHP_EOL;
  $info = waitForTransactionInfo($txid);
  echo 'state => ' . $info->receipt->result . PHP_EOL;
  if($info->receipt->result != 'SUCCESS'){
    throw new Exception('failed to deploy: ' . $name);
  }
  
  $address = Address::encode($info->contract_address);
  echo 'contract address => ' . $address . PHP_EOL;
  return $address;
}

function initialize_factory($factoryAddr){
  global $api, $alice;
  
  echo '>>initialize factory...' . PHP_EOL;
  
  echo 'load contract abi...' . PHP_EOL;
  $abi = file_get_contents('../contracts/build/JustswapFactory.abi');
  
  $factory = new Contract($api, $abi, $alice);
  $factory->at($factoryAddr);
  $txid = $factory->initializeFactory($alice->address()->base58(), []);
  echo 'txid => ' . $txid . PHP_EOL;
  
  echo 'waiting for transaction info...' . PHP_EOL;
  $info = waitForTransactionInfo($txid);
  echo 'state => ' . $info->receipt->result . PHP_EOL;
  if($info->receipt->result != 'SUCCESS'){
    throw new Exception('failed to deploy: ' . $name);
  }  
}

function save_addresses($addresses){
  echo '>>save contract address to addresses.json...' . PHP_EOL;
  file_put_contents('addresses.json', json_encode($addresses, JSON_PRETTY_PRINT));
}

$addresses = [];

$addresses['HubToken'] = deploy_contract('HubToken');
$addresses['WizToken'] = deploy_contract('WizToken');
$addresses['JustswapFactory'] = deploy_contract('JustswapFactory');

initialize_factory($addresses['JustswapFactory']);

save_addresses($addresses);

