<?php
namespace JustSwap;

use Exception;
use Web3\Web3;
use Web3\Utils;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use phpseclib\Math\BigInteger;

const ABIs = [
  'TRC20' => '[{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"},{"name":"_spender","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"payable":true,"stateMutability":"payable","type":"fallback"},{"anonymous":false,"inputs":[{"indexed":true,"name":"owner","type":"address"},{"indexed":true,"name":"spender","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"}]',
  'FACTORY' => '[{"constant":true,"inputs":[{"name":"token","type":"address"}],"name":"getExchange","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"exchange","type":"address"},{"name":"token","type":"address"}],"name":"registerExchange","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"token","type":"address"}],"name":"createExchange","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"exchangeTemplate","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"template","type":"address"}],"name":"initializeFactory","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"exchange","type":"address"}],"name":"getToken","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"tokenCount","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"token_id","type":"uint256"}],"name":"getTokenWithId","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"anonymous":false,"inputs":[{"indexed":true,"name":"token","type":"address"},{"indexed":true,"name":"exchange","type":"address"}],"name":"NewExchange","type":"event"}]',
  'EXCHANGE' => '[{"constant":false,"inputs":[{"name":"tokens_sold","type":"uint256"},{"name":"min_trx","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"}],"name":"tokenToTrxTransferInput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"value","type":"uint256"}],"name":"approve","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"trx_bought","type":"uint256"},{"name":"max_tokens","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"}],"name":"tokenToTrxTransferOutput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"from","type":"address"},{"name":"to","type":"address"},{"name":"value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"min_liquidity","type":"uint256"},{"name":"max_tokens","type":"uint256"},{"name":"deadline","type":"uint256"}],"name":"addLiquidity","outputs":[{"name":"","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"min_tokens","type":"uint256"},{"name":"deadline","type":"uint256"}],"name":"trxToTokenSwapInput","outputs":[{"name":"","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"token_addr","type":"address"}],"name":"setup","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"tokens_bought","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"}],"name":"trxToTokenTransferOutput","outputs":[{"name":"","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"function"},{"constant":true,"inputs":[{"name":"input_amount","type":"uint256"},{"name":"input_reserve","type":"uint256"},{"name":"output_reserve","type":"uint256"}],"name":"getInputPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"tokens_sold","type":"uint256"}],"name":"getTokenToTrxInputPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"factoryAddress","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"trx_sold","type":"uint256"}],"name":"getTrxToTokenInputPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"tokens_bought","type":"uint256"},{"name":"max_tokens_sold","type":"uint256"},{"name":"max_trx_sold","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"},{"name":"exchange_addr","type":"address"}],"name":"tokenToExchangeTransferOutput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"tokens_sold","type":"uint256"},{"name":"min_trx","type":"uint256"},{"name":"deadline","type":"uint256"}],"name":"tokenToTrxSwapInput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"trx_bought","type":"uint256"},{"name":"max_tokens","type":"uint256"},{"name":"deadline","type":"uint256"}],"name":"tokenToTrxSwapOutput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"tokenAddress","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"tokens_bought","type":"uint256"}],"name":"getTrxToTokenOutputPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"tokens_bought","type":"uint256"},{"name":"deadline","type":"uint256"}],"name":"trxToTokenSwapOutput","outputs":[{"name":"","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"to","type":"address"},{"name":"value","type":"uint256"}],"name":"transfer","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"tokens_bought","type":"uint256"},{"name":"max_tokens_sold","type":"uint256"},{"name":"max_trx_sold","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"token_addr","type":"address"}],"name":"tokenToTokenSwapOutput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"tokens_sold","type":"uint256"},{"name":"min_tokens_bought","type":"uint256"},{"name":"min_trx_bought","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"exchange_addr","type":"address"}],"name":"tokenToExchangeSwapInput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"min_tokens","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"}],"name":"trxToTokenTransferInput","outputs":[{"name":"","type":"uint256"}],"payable":true,"stateMutability":"payable","type":"function"},{"constant":true,"inputs":[{"name":"owner","type":"address"},{"name":"spender","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"tokens_sold","type":"uint256"},{"name":"min_tokens_bought","type":"uint256"},{"name":"min_trx_bought","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"token_addr","type":"address"}],"name":"tokenToTokenSwapInput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"tokens_bought","type":"uint256"},{"name":"max_tokens_sold","type":"uint256"},{"name":"max_trx_sold","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"exchange_addr","type":"address"}],"name":"tokenToExchangeSwapOutput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"tokens_sold","type":"uint256"},{"name":"min_tokens_bought","type":"uint256"},{"name":"min_trx_bought","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"},{"name":"exchange_addr","type":"address"}],"name":"tokenToExchangeTransferInput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"trx_bought","type":"uint256"}],"name":"getTokenToTrxOutputPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"tokens_bought","type":"uint256"},{"name":"max_tokens_sold","type":"uint256"},{"name":"max_trx_sold","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"},{"name":"token_addr","type":"address"}],"name":"tokenToTokenTransferOutput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"tokens_sold","type":"uint256"},{"name":"min_tokens_bought","type":"uint256"},{"name":"min_trx_bought","type":"uint256"},{"name":"deadline","type":"uint256"},{"name":"recipient","type":"address"},{"name":"token_addr","type":"address"}],"name":"tokenToTokenTransferInput","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"amount","type":"uint256"},{"name":"min_trx","type":"uint256"},{"name":"min_tokens","type":"uint256"},{"name":"deadline","type":"uint256"}],"name":"removeLiquidity","outputs":[{"name":"","type":"uint256"},{"name":"","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"output_amount","type":"uint256"},{"name":"input_reserve","type":"uint256"},{"name":"output_reserve","type":"uint256"}],"name":"getOutputPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"payable":true,"stateMutability":"payable","type":"fallback"},{"anonymous":false,"inputs":[{"indexed":true,"name":"buyer","type":"address"},{"indexed":true,"name":"trx_sold","type":"uint256"},{"indexed":true,"name":"tokens_bought","type":"uint256"}],"name":"TokenPurchase","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"buyer","type":"address"},{"indexed":true,"name":"tokens_sold","type":"uint256"},{"indexed":true,"name":"trx_bought","type":"uint256"}],"name":"TrxPurchase","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"provider","type":"address"},{"indexed":true,"name":"trx_amount","type":"uint256"},{"indexed":true,"name":"token_amount","type":"uint256"}],"name":"AddLiquidity","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"provider","type":"address"},{"indexed":true,"name":"trx_amount","type":"uint256"},{"indexed":true,"name":"token_amount","type":"uint256"}],"name":"RemoveLiquidity","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"operator","type":"address"},{"indexed":true,"name":"trx_balance","type":"uint256"},{"indexed":true,"name":"token_balance","type":"uint256"}],"name":"Snapshot","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"owner","type":"address"},{"indexed":true,"name":"spender","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Approval","type":"event"}]'
];

class SwapKit{
  const TRX_TOKEN = 'T9yD14Nj9j7xAB4dbGeiX9h8vMa2GfnLve';
  const ZERO_ADDRESS = 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb';
  const I256_UNLIMITED = '0x7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff';
  const U256_UNLIMITED = '0xffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff';
  const DEFAULT_DEADLINE_SINCE_NOW = 10*60; // 10 minutes 
    
  public $api;
  public $credential;
  public $factoryAddress;
  
  protected $cache = [];
  protected $tradeBuilder;
  protected $orderBuilder;
  
  public function __construct($api, $credential, $factoryAddress){
    $this->api = $api;
    $this->credential = $credential;
    $this->factoryAddress = $factoryAddress;
  }
  
  public function getCredential(){
    return $this->credential;
  }
  
  public function getDefaultAddress(){
    return $this->credential->address()->base58();
  }
  
  public function getApi(){
    return $this->api;
  }
    
  function getTrxBalance($address){
    return $this->api->getBalance($address);
  }
    
  function waitForConfirmation($txid, $tries = 60){
    $info = $this->waitForTransactionInfo($txid, $tries);
    return $info->receipt->result == 'SUCCESS' ? true : false;
  }
  
  function waitForTransactionInfo($txid, $tries = 60){
    for($i=0; $i<$tries; $i++){
      $info = $this->api->getTransactionInfo($txid);
      if(isset($info->receipt)) {
        return $info;
      }
      sleep(3); //idle for 3 seconds
    }
    
    throw new Exception('failed to get transaction info');
  }
    
  public function getContract($address, $abi){
    if(isset($this->cache[$address])) {
      return $this->cache[$address];
    }
    
    $inst = new Contract($this->api, $abi, $this->credential);
    $inst->at($address);
    
    $this->cache[$address] = $inst;
    
    return $inst;
  }
  
  public function getFactory(){
    return $this->getContract($this->factoryAddress, ABIs['FACTORY']);
  }
  
  public function getExchange($addr){
    return $this->getContract($addr, ABIs['EXCHANGE']);
  }
  
  public function hasExchangeForToken($tokenAddr){
    $addr = $this->getFactory()->call('getExchange', $tokenAddr);
    return $addr == self::ZERO_ADDRESS ? false: true;
  }
  
  public function getExchangeForToken($tokenAddr){
    $addr = $this->getFactory()->call('getExchange', $tokenAddr);
    if($addr == self::ZERO_ADDRESS) {
      throw new Exception('no exchange for this token: ' . $tokenAddr);
    }
    return $this->getExchange($addr);
  }
  
  public function getToken($tokenAddr){
    return $this->getContract($tokenAddr, ABIs['TRC20']);
  }
                 
  public function getExchangeReserves($exchangeAddr, $tokenAddr){
    $reserveTrx = $this->getTrxBalance($exchangeAddr);    
    $reserveToken = $this->getToken($tokenAddr)->call('balanceOf', $exchangeAddr);
    return [bn($reserveTrx), $reserveToken];
  }
  
  public function getExchangeReservesForToken($tokenAddr){
    $exchangeAddr = $this->getFactory()->call('getExchange', $tokenAddr);
    if($exchangeAddr == self::ZERO_ADDRESS) {
      throw new Exception('no exchange for this token: ' . $tokenAddr);
    }
    return $this->getExchangeReserves($exchangeAddr, $tokenAddr);
  }
  
  public function isExchangeEmptyForToken($tokenAddr){
    list($ra, $rb) = $this->getExchangeReservesForToken($tokenAddr);
    return $ra->multiply($rb)->compare(bn(0)) == 0 ? true : false;
  }

  public function getExchangeQuoteForToken($amount, $tokenAddr, $reverse = false){
    list($reserveTrx, $reserveToken) = $this->getExchangeReservesForToken($tokenAddr);
    if($reverse){ //token -> trx
      list($q, $r) = $amount->multiply($reserveTrx)->divide($reserveToken);
      return $q;
    }else{
      list($q, $r) = $amount->multiply($reserveToken)->divide($reserveTrx);
      return $q;
    }
  }
  
  public function getExchangeMintForToken($amount, $tokenAddr){
    $exchange = $this->getExchangeForToken($tokenAddr);
    $totalLiquidity = $exchange->totalSupply();
    $reserveTrx = $this->getTrxBalance($exchange->at());
    list($mint, $r) = $amount->multiply($totalLiquidity)->divide(bn($reserveTrx));
    return $mint;
  }
  
  public function getAmountOut($amountIn, $tokenIn, $tokenOut){    
    if($tokenIn == self::TRX_TOKEN && $tokenOut != self::TRX_TOKEN){
      return $this->getExchangeForToken($tokenOut)->getTrxToTokenInputPrice($amountIn);
    }  
    if($tokenIn != self::TRX_TOKEN && $tokenOut == self::TRX_TOKEN){
      return $this->getExchangeForToken($tokenIn)->getTokenToTrxInputPrice($amountIn);
    }
    if($tokenIn != self::TRX_TOKEN && $tokenOut != self::TRX_TOKEN){
      $amountTrx = $this->getExchangeForToken($tokenIn)->getTokenToTrxInputPrice($amountIn);
      $amountOut = $this->getExchangeForToken($tokenOut)->getTrxToTokenInputPrice($amountTrx);
      return $amountOut;
    }
    throw new Exception('bad token pair');
  }
  
  public function getAmountIn($amountOut, $tokenIn, $tokenOut){
    if($tokenIn == self::TRX_TOKEN && $tokenOut != self::TRX_TOKEN){
      return $this->getExchangeForToken($tokenOut)->getTrxToTokenOutputPrice($amountOut);
    }
    if($tokenIn != self::TRX_TOKEN && $tokenOut == self::TRX_TOKEN){
      return $this->getExchangeForToken($tokenIn)->getTokenToTrxOutputPrice($amountOut);
    }
    if($tokenIn != self::TRX_TOKEN && $tokenOut != self::TRX_TOKEN){
      $amountTrx = $this->getExchangeForToken($tokenOut)->getTrxToTokenOutputPrice($amountOut);
      $amountIn = $this->getExchangeForToken($tokenIn)->getTokenToTrxOutputPrice($amountTrx);
      return $amountIn;
    }
    throw new Exception('bad token pair');
  }
    
  public function getSlippageRange($amount, $slipNumerator, $slipDenominator) { 
    list($min, $r) = $amount->multiply($slipDenominator->subtract($slipNumerator))->divide($slipDenominator);
    list($max, $r) = $amount->multiply($slipDenominator->add($slipNumerator))->divide($slipDenominator);
    return [$min, $max];
  }
  
  public function defaultDeadline(){
    return time() + self::DEFAULT_DEADLINE_SINCE_NOW;
  }
  
  public function getExchangeLiquidityQuoteForToken($liquidity, $tokenAddr){
    $exchange = $this->getExchangeForToken($tokenAddr);
    $totalLiquidity = $exchange->totalSupply();
    list($reserveTrx, $reserveToken) = $this->getExchangeReserves($exchange->at(), $tokenAddr);
    list($amountTrx, $r) = $liquidity->multiply($reserveTrx)->divide($totalLiquidity);
    list($amountToken, $r) = $liquidity->multiply($reserveToken)->divide($totalLiquidity);
    return [$amountTrx, $amountToken];
  }
    
  public function getExchangeLiquidityPositionForToken($account, $tokenAddr){
    $exchange = $this->getExchangeForToken($tokenAddr);
    $totalSupply = $exchange->totalSupply();
    $balance = $exchange->balanceOf($account);
    list($q, $r) = $balance->multiply(bn(10000))->divide($totalSupply);
    $share = intval($q->toString()) / 100;
    return (object)[
      'totalSupply' => $totalSupply,
      'balance' => $balance,
      'share' => $share
    ];
  }
  
  
  public function getTradeBuilder(){
    if(!isset($this->tradeBuilder)){
      $this->tradeBuilder = new TradeBuilder($this);
    }
    return $this->tradeBuilder->reset();
  }
  
  public function getOrderBuilder(){
    if(!isset($this->orderBuilder)){
      $this->orderBuilder = new OrderBuilder($this);
    }
    return $this->orderBuilder->reset();
  }
  
  public function executeTrade($trade, $opts = []){
    switch($trade->tradeType){
      case Trade::EXACT_INPUT:
        if(strcasecmp($trade->tokenIn, self::TRX_TOKEN) == 0){
          $opts = array_merge($opts, ['value' => intval($trade->amountIn->toString())]);
          return $this->getExchangeForToken($trade->tokenOut)->trxToTokenTransferInput(
            $trade->amountOutMin, 
            $this->defaultDeadline(),
            $trade->to,
            $opts
          );
        }
        if(strcasecmp($trade->tokenOut, self::TRX_TOKEN) == 0){
          return $this->getExchangeForToken($trade->tokenIn)->tokenToTrxTransferInput(
            $trade->amountIn,
            $trade->amountOutMin,
            $this->defaultDeadline(),
            $trade->to,
            $opts
          );
        }        
        return $this->getExchangeForToken($trade->tokenIn)->tokenToTokenTransferInput(
          $trade->amountIn,
          $trade->amountOutMin,
          bn(1), //min trx in middle
          $this->defaultDeadline(),
          $trade->to,
          $trade->tokenOut,
          $opts
        );
      case Trade::EXACT_OUTPUT:
        if(strcasecmp($trade->tokenIn, self::TRX_TOKEN) == 0){
          $opts = array_merge($opts, ['value' => intval($trade->amountInMax->toString()) ]);
          return $this->getExchangeForToken($trade->tokenOut)->trxToTokenTransferOutput(
            $trade->amountOut,
            $this->defaultDeadline(),
            $trade->to,
            $opts
          );
        }
        if(strcasecmp($trade->tokenOut, self::TRX_TOKEN) == 0){
          return $this->getExchangeForToken($trade->tokenIn)->tokenToTrxTransferOutput(
            $trade->amountOut,
            $trade->amountInMax,
            $this->defaultDeadline(),
            $trade->to,
            $opts
          );
        }
        return $this->getExchangeForToken($trade->tokenOut)->tokenToTokenTransferOutput(
          $trade->amountOut,
          $trade->amountInMax,
          self::I256_UNLIMITED,
          $this->defaultDeadline(),
          $trade->to,
          $trade->tokenIn,
          $opts
        );
    }
    throw new Exception('trade type not supported');
  }
  
  public function executeOrder($order, $opts = []){
    switch($order->orderType){
      case Order::LIQUIDITY_ADD:
        if(strcasecmp($order->tokenA, self::TRX_TOKEN) == 0){
          $liquidityMin = $order->liquidityMin;
          $amountBMax = $order->amountBMax;
          if($order->genesis){
            $liquidityMin = bn(1);
            $amountBMax = $order->amountB;
          }
          $opts = array_merge($opts, ['value' => intval($order->amountA->toString())]);
          return $this->getExchangeForToken($order->tokenB)->addLiquidity(
            $liquidityMin,
            $amountBMax,
            $this->defaultDeadline(),
            $opts
          );
        }
        throw new Exception('bad liquidity order');
      case Order::LIQUIDITY_REMOVE:
        if(strcasecmp($order->tokenA, self::TRX_TOKEN) == 0){
          return $this->getExchangeForToken($order->tokenB)->removeLiquidity(
            $order->liquidity,
            $order->amountAMin,
            $order->amountBMin,
            $this->defaultDeadline(),
            $opts
          );
        }
        throw new Exception('bad liquidity order');
    }
    throw new Exception('order type not supported');
  }
}