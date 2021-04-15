<?php
namespace Justswap;

use Exception;

class Trade{
  const EXACT_INPUT = 'XI';
  const EXACT_OUTPUT = 'XO';
  
  public $tradeType;
  public $tokenIn;
  public $tokenOut;
  public $amountIn;
  public $amountOut;
  public $slippage; // 0 - 1000
  public $to;
  
  public function __construct($tradeType, $tokenIn, $tokenOut, $amountIn, $amountOut, $slippage, $to){
    $this->tradeType = $tradeType;
    $this->tokenIn = $tokenIn;
    $this->tokenOut = $tokenOut;
    $this->amountIn = $amountIn;
    $this->amountOut = $amountOut;
    $this->slippage = $slippage;
    $this->to = $to;
  }
  
  public function __get($name){
    if($name == 'amountInMin' || $name == 'amountInMax'){
      list($min, $max) = $this->getSlippageRange($this->amountIn, $this->slippage, bn(1000));
      return $name == 'amountInMin' ? $min : $max;
    }
    if($name == 'amountOutMin' || $name == 'amountOutMax'){
      list($min, $max) = $this->getSlippageRange($this->amountOut, $this->slippage, bn(1000));
      return $name == 'amountOutMin' ? $min : $max;
    }
    throw new Exception('property not supported.');
  }
  
  public function __toString(){
    return sprintf(
      'Trade[tradeType: %s, tokenIn: %s, tokenOut: %s, amountIn: %s, amountOut: %s, slippage: %s, to: %s]',
      $this->tradeType,
      $this->tokenIn,
      $this->tokenOut,
      $this->amountIn,
      $this->amountOut,
      $this->slippage,
      $this->to
    );
  }
  
  private function getSlippageRange($amount, $slipNumerator, $slipDenominator) { 
    list($min, $r) = $amount->multiply($slipDenominator->subtract($slipNumerator))->divide($slipDenominator);
    list($max, $r) = $amount->multiply($slipDenominator->add($slipNumerator))->divide($slipDenominator);
    return [$min, $max];
  }
  
}