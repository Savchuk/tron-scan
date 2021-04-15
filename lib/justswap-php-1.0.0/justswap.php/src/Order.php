<?php
namespace JustSwap;

class Order{
  const LIQUIDITY_ADD = 'LA';
  const LIQUIDITY_REMOVE = 'LR';

  public $orderType;
  public $tokenA;
  public $tokenB;
  public $amountA;
  public $amountB;
  public $liquidity;
  public $slippage;
  public $genesis;
  
  public function __construct($orderType, $tokenA, $tokenB, $amountA, $amountB, $liquidity, $slippage, $genesis){
    $this->orderType = $orderType;
    $this->tokenA = $tokenA;
    $this->tokenB = $tokenB;
    $this->amountA = $amountA;
    $this->amountB = $amountB;
    $this->liquidity = $liquidity;
    $this->slippage = $slippage;
    $this->genesis = $genesis;
  }
    
  public function __get($name){
    if($name == 'amountAMin' || $name == 'amountAMax'){
      list($min, $max) = $this->getSlippageRange($this->amountA, $this->slippage, bn(1000));
      return $name == 'amountAMin' ? $min : $max;
    }
    if($name == 'amountBMin' || $name == 'amountBMax'){
      list($min, $max) = $this->getSlippageRange($this->amountB, $this->slippage, bn(1000));
      return $name == 'amountBMin' ? $min : $max;
    }
    if($name == 'liquidityMin' || $name == 'liquidityMax'){
      list($min, $max) = $this->getSlippageRange($this->liquidity, $this->slippage, bn(1000));
      return $name == 'liquidityMin' ? $min : $max;
    }    
    throw new Exception('property not supported.');
  }  
  
  private function getSlippageRange($amount, $slipNumerator, $slipDenominator) { 
    list($min, $r) = $amount->multiply($slipDenominator->subtract($slipNumerator))->divide($slipDenominator);
    list($max, $r) = $amount->multiply($slipDenominator->add($slipNumerator))->divide($slipDenominator);
    return [$min, $max];
  }
 
  public function __toString(){
    return sprintf(
      'Order[orderType: %s, tokenA: %s, tokenB: %s, amountA: %s, amountB: %s, liquidity: %s, slippage: %s, genesis: %b]',
      $this->orderType,
      $this->tokenA,
      $this->tokenB,
      $this->amountA,
      $this->amountB,
      $this->liquidity,
      $this->slippage,
      $this->genesis
    );
  }
 
}