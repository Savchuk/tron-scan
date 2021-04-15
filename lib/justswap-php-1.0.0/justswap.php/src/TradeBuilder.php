<?php
namespace Justswap;

class TradeBuilder{
  protected $kit;
  
  protected $tradeType;
  protected $amountIn;
  protected $amountOut;
  protected $tokenIn;
  protected $tokenOut;
  protected $slippage;
  protected $to;
  
  public function __construct($kit){
    $this->kit = $kit;
    
    $this->reset();
  }
  
  public function reset(){
    unset($this->tradeType);
    unset($this->amountIn);
    unset($this->amountOut);
    unset($this->tokenIn);
    unset($this->tokenOut);
    unset($this->to);
    $this->slippage = bn(5);  // 0.5%
    return $this;
  }
  
  public function tradeType($tradeType){
    $this->tradeType = $tradeType;
    return $this;
  }
  
  public function amountIn($amountIn){
    $this->amountIn = $amountIn;
    return $this;
  }
  
  public function amountOut($amountOut){
    $this->amountOut = $amountOut;
    return $this;
  }
  
  public function tokenIn($tokenIn){
    $this->tokenIn = $tokenIn;
    return $this;
  }
  
  public function tokenOut($tokenOut){
    $this->tokenOut = $tokenOut;
    return $this;
  }
    
  public function slippage($slippage){
    $this->slippage = $slippage;
    return $this;
  }
  
  public function to($to){
    $this->to = $to;
    return $this;
  }
  
  public function build(){
    if(!isset($this->tradeType)){
      throw new Exception('trade type not set');
    }
    if(!isset($this->tokenIn) || !isset($this->tokenOut)){
      throw new Exception('input & output token not set');
    }
    if(!isset($this->to)){
      throw new Exception('to address not set');
    }
    if($this->tradeType == Trade::EXACT_INPUT){
      if(!isset($this->amountIn)){
        throw new Exception('input amount not set');
      }
      $amountOut = $this->kit->getAmountOut($this->amountIn, $this->tokenIn, $this->tokenOut);
      
      $trade = new Trade(
        $this->tradeType,
        $this->tokenIn,
        $this->tokenOut,
        $this->amountIn,
        $amountOut,
        $this->slippage,
        $this->to
      );
      return $trade;
    }
    
    if($this->tradeType == Trade::EXACT_OUTPUT){
      if(!isset($this->amountOut)){
        throw new Exception('output amount not set');
      }
      $amountIn = $this->kit->getAmountIn($this->amountOut, $this->tokenIn, $this->tokenOut);
      
      $trade = new Trade(
        $this->tradeType,
        $this->tokenIn,
        $this->tokenOut,
        $amountIn,
        $this->amountOut,
        $this->slippage,
        $this->to
      );
      return $trade;
    }
    
  }
}