<?php
namespace JustSwap;

use Exception;
use InvalidArgumentException;
use Web3\Utils;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address as EthAddress;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use Web3\Validators\AddressValidator;
use Web3\Validators\HexValidator;
use Web3\Formatters\AddressFormatter;
use Web3\Validators\StringValidator;

class TronAddress extends EthAddress{
  function inputFormat($value, $name){
    $hex = Address::decode($value);
    return parent::inputFormat($hex,$name);
  }
  public function outputFormat($value, $name){
    $hex = parent::outputFormat($value,$name);
    $hex = preg_replace('/^0x/', '41', $hex); //2020-11-1
    return Address::encode($hex);
  }
}

class Contract{
  protected $api;
  protected $abi;
  protected $ethabi;
  protected $constructor = [];
  protected $functions = [];
  protected $events = [];
  
  protected $readMethods = [];
  protected $writeMethods = [];
  
  protected $toAddress;
  protected $bytecode;
  
  protected $credential;
  
  function __construct($tronApi,$abi,$credential=null){
    
    $abi = Utils::jsonToArray($abi, 5);
    
    foreach ($abi as $item) {
      if (isset($item['type'])) {
        if (strcasecmp($item['type'], 'function') == 0) {
          $this->functions[$item['name']] = $item;
          
          if(isset($item['stateMutability'])){
            if(strcasecmp($item['stateMutability'], 'view') == 0 ||
               strcasecmp($item['stateMutability'], 'pure') == 0)
            {
              $this->readMethods[] = $item['name'];
            }else{
              $this->writeMethods[] = $item['name'];
            }
          }
          
        } elseif (strcasecmp($item['type'], 'constructor') == 0) {
          $this->constructor = $item;
        } elseif (strcasecmp($item['type'], 'event') == 0) {
          $this->events[$item['name']] = $item;
        }
      }
    }
        
    $this->abi = $abi;
    
    $this->api = $tronApi;
    
    $this->credential = $credential;
    
    $this->ethabi = new Ethabi([
        'address' => new TronAddress,
        'bool' => new Boolean,
        'bytes' => new Bytes,
        'dynamicBytes' => new DynamicBytes,
        'int' => new Integer,
        'string' => new Str,
        'uint' => new Uinteger,
    ]);
    
  }
    
  function __call($name, $args){
    if(in_array($name, $this->writeMethods)){
      return $this->send($name, ...$args);
    }
    if(in_array($name, $this->readMethods)){
      return $this->call($name, ...$args);
    }
    throw new Exception('bad method: ' . $name);
  }  
    
  function at($address = null) {
    if(!isset($address)){
      return $this->toAddress;
    }
    //$this->toAddress = Address::fromBase58($address);
    $this->toAddress = $address;
    return $this;
  }
  
  function bytecode($bytecode = null){
    if(!isset($bytecode)){
      return $this->bytecode;
    }
    $this->bytecode = Utils::stripZero($bytecode);  
    return $this;
  }

  function credential($credential = null){
    if(!isset($credential)){
      return $this->credential;
    }
    $this->credential = $credential;
    return $this;
  }
  
  public function deploy($contractName)
  {
    if(is_null($this->credential)){
      throw new Exception('Sender credential not set.');
    }
    
    if (isset($this->constructor)) {
      $constructor = $this->constructor;
      $arguments = func_get_args();
      array_shift($arguments); //shift out contract name
      
      if(!isset($constructor['inputs'])){ //sometimes contract has no constructor defined
        $constructor['inputs'] = [];
      }      

      if (count($arguments) < count($constructor['inputs'])) {
          throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
      }
      if (!isset($this->bytecode)) {
          throw new InvalidArgumentException('Please call bytecode($bytecode) before new().');
      }
      $params = array_splice($arguments, 0, count($constructor['inputs']));
      $data = $this->ethabi->encodeParameters($constructor, $params);
      $data = substr($data,2);
      
      $tx = $this->api->deployContract(
        $this->abi,
        $this->bytecode,
        $data,
        $contractName,
        0,
        $this->credential->address()->base58()
      );
      $signedTx = $this->credential->signTx($tx);
      //var_dump($signedTx);
      $ret = $this->api->broadcastTransaction($signedTx);
      
      if(!isset($ret->result) || !$ret->result){
        throw new Exception('submit tx error');
      }
      
      return $signedTx->txID;
    }
  }

  
  function send()
  {
    if(is_null($this->credential)){
      throw new Exception('Sender credential not set.');
    }
    
    if (isset($this->functions)) {
      $arguments = func_get_args();
      $method = array_shift($arguments); //method name
      $opts = array_pop($arguments);     //tx opts

      if (!is_string($method) || !isset($this->functions[$method])) {
          throw new InvalidArgumentException('Please make sure the method exists.');
      }
      $function = $this->functions[$method];

      if(!isset($function['inputs'])){ //patch 2020-11-2
        $function['inputs'] = [];
      }

      if (count($arguments) < count($function['inputs'])) {
          throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
      }
      
      $params = array_splice($arguments, 0, count($function['inputs']));
      $data = $this->ethabi->encodeParameters($function, $params);
      $data = substr($data,2);
      $functionName = Utils::jsonMethodToString($function);
      
      $opts = array_merge([
        'value' => 0,
        'feeLimit' => 1000000000,
        'bandwidthLimit' => 0
      ], $opts);
            
      $ret = $this->api->triggerSmartContract(
        $this->toAddress,
        $functionName,
        $data,
        $opts['value'],
        $this->credential->address()->base58()
      );
      
      if($ret->result->result == false){
        throw new Exception('Error build contract transaction.');
      }      
      $signedTx = $this->credential->signTx($ret->transaction);
      $ret = $this->api->broadcastTransaction($signedTx);
      
      if(!isset($ret->result) || !$ret->result){
        throw new Exception('submit tx error');
      }
      
      return $signedTx->txID;
    }
  }
  
  function call()
  {
    if(is_null($this->credential)){
      throw new Exception('Sender credential not set.');
    }
    
    if (isset($this->functions)) {
      $arguments = func_get_args();
      //$method = array_splice($arguments, 0, 1)[0];
      $method = array_shift($arguments);
      
      if (!is_string($method) || !isset($this->functions[$method])) {
          throw new InvalidArgumentException('Please make sure the method exists.');
      }
      $function = $this->functions[$method];
      
      if(!isset($function['inputs'])){  //patch 2020-11-2
        $function['inputs'] = [];
      }

      if (count($arguments) < count($function['inputs'])) {
          throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
      }
      
      $params = array_splice($arguments, 0, count($function['inputs']));
      $data = $this->ethabi->encodeParameters($function, $params);
      $data = substr($data,2);
      $functionName = Utils::jsonMethodToString($function);
      
      $ret = $this->api->triggerConstantContract( // 2020-11-2
        $this->toAddress,
        $functionName,
        $data,
        $this->credential->address()->base58()
      );
      
      if($ret->result->result == false){
        throw new Exception('Error build contract transaction.');
      }      
      $decoded = $this->ethabi->decodeParameters($function,$ret->constant_result[0]);
      $ret = array_values($decoded);
      if(count($ret) == 1) return $ret[0];  //return first element if length is one
      else return $ret;
    }
  }
  
  function events($since = 0){
    $ret = $this->api->getContractEvents($this->toAddress,$since);
    return $ret;
  }

}