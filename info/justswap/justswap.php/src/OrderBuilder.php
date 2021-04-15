<?php
namespace JustSwap;

use Exception;

class OrderBuilder{
  protected $kit;

  protected $orderType;
  protected $tokenA; //fixed to trx
  protected $tokenB;
  protected $amountA;
  protected $amountB;
  protected $liquidity;
  protected $slippage;
  
  public function __construct($kit){
    $this->kit = $kit;
    
    $this->reset();
  }
  
  public function reset(){
    unset($this->orderType);
    $this->tokenA = SwapKit::TRX_TOKEN;
    unset($this->tokenB);
    unset($this->amountA);
    unset($this->amountB);
    unset($this->liquidity);
    unset($this->to);
    $this->slippage = bn(5); // 0.5%
    return $this;
  }
  
  public function orderType($orderType){
    $this->orderType = $orderType;
    return $this;
  }
    
  public function tokenA($tokenAddr){
    if(strcasecmp($tokenAddr, SwapKit::TRX_TOKEN) != 0){
      throw new Exception('token a must be trx');
    }
    $this->tokenA = $tokenAddr;
    return $this;
  }  
    
  public function tokenB($tokenAddr){
    $this->tokenB = $tokenAddr;
    return $this;
  }
  
  public function amountA($amount){
    $this->amountA = $amount;
    return $this;
  }
  
  public function amountB($amount){
    $this->amountB = $amount;
    return $this;
  }
  
  public function liquidity($liquidity){
    $this->liquidity = $liquidity;
    return $this;
  }
  
  public function slippage($slippage){
    $this->slippage = $slippage;
    return $this;
  }
    
  public function build(){
    if(!isset($this->orderType)){
      throw new Exception('order type not set');
    }
    
    if(!isset($this->tokenB)){
      throw new Exception('token not set');
    }
    
    if($this->orderType == Order::LIQUIDITY_ADD){
      $hasExchange = $this->kit->hasExchangeForToken($this->tokenB);
      
      if(!$hasExchange){ 
        throw new Exception('exchange not exists for token: ' . $this->tokenB);
      }
      
      list($reserveA, $reserveB) = $this->kit->getExchangeReservesForToken($this->tokenB);

      $genesis = false;
      $liquidity = bn(1);
      if($reserveA->compare(bn(0)) == 0){//first time
        if(!isset($this->amountA) || !isset($this->amountB)){
          throw new Exception('trx or token amount not set');
        }
        if($this->amountA->compare(bn(10000000)) < 0){
          throw new Exception('at least 10 trx is required to init liquidity pool.');
        }        
        $genesis = true;
      }else{ // add more
        if(isset($this->amountA)){
          $this->amountB = $this->kit->getExchangeQuoteForToken($this->amountA, $this->tokenB, false);
        }
        else if(isset($this->amountB)){
          $this->amountA = $this->kit->getExchangeQuoteForToken($this->amountB, $this->tokenB, true);
        }
        $liquidity = $this->kit->getExchangeMintForToken($this->amountA, $this->tokenB);
      }
      
      return new Order(
        $this->orderType,
        $this->tokenA, 
        $this->tokenB,
        $this->amountA,
        $this->amountB,
        $liquidity,
        $this->slippage,
        $genesis        
      );      
    }
    
    if($this->orderType == Order::LIQUIDITY_REMOVE){
      if(!isset($this->liquidity)){
        throw new Exception('liquidity not set');
      }
      list($amountA, $amountB) = $this->kit->getExchangeLiquidityQuoteForToken($this->liquidity, $this->tokenB);
      return new Order(
        $this->orderType,
        $this->tokenA,
        $this->tokenB,
        $amountA,
        $amountB,
        $this->liquidity,
        $this->slippage,
        false
      );
    }
    
  }
}