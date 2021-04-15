<?php


namespace App\Library\Traits;


use JustSwap\SwapKit;

trait PriceTrait
{

    function getPrice($amountIn, $tokenIn, $tokenOut){

        try {
            $price = (int)$this->kit->getAmountOut(bn($amountIn), $tokenIn, $tokenOut)->__toString();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            echo $e->getResponse()->getStatusCode() . " - " . $amountIn . " - " . $tokenIn . "-".  $tokenOut . PHP_EOL;
            $price = 0;
        }
        return $price;
    }

    function getPriceEthToUsdt($amount)
    {
        $decimalEth = self::$decimalEth;
        $amountIn = number_format($amount * pow(10, $decimalEth), $decimalEth, '.','');
        $decimalUsdt = self::$decimalUsdt;

        $tokenIn =  self::$tokenEth;
        $tokenOut = self::$tokenUsdt;

        $price = $this->getPrice($amountIn, $tokenIn, $tokenOut);
        $price = number_format($price * pow(10, -$decimalUsdt), $decimalUsdt, '.','');

        return $price;
    }

    function getPriceTrxToUsdt($amount)
    {
        $decimalTrx = self::$decimalTrx;
        $amountIn = $amount * pow(10, $decimalTrx);
        $decimalUsdt = self::$decimalUsdt;

        $tokenIn =  SwapKit::TRX_TOKEN;
        $tokenOut = self::$tokenUsdt;

        $price = $this->getPrice($amountIn, $tokenIn, $tokenOut);
        $price = number_format($price * pow(10, -$decimalUsdt), $decimalUsdt, '.','');

        return $price;
    }

    function getPriceBtcToUsdt($amount)
    {
        $decimalBtc = self::$decimalBtc;
        $amountIn = $amount * pow(10, $decimalBtc);
        $decimalUsdt = self::$decimalUsdt;

        $tokenIn =  self::$tokenBtc;
        $tokenOut = self::$tokenUsdt;

        $price = $this->getPrice($amountIn, $tokenIn, $tokenOut);
        $price = number_format($price * pow(10, -$decimalUsdt), $decimalUsdt, '.','');

        return $price;
    }


    function getPriceUsdtToToken($amountUsdt,$token)
    {
        $tokens = $this->tokens;

        $decimalIn = $tokens['USDT']['decimal'];
        $amountIn = number_format($amountUsdt * pow(10, $decimalIn), $decimalIn, '.','');
        $decimalOut = $tokens[$token]['decimal'];

        $tokenIn = $tokens['USDT']['address'];
        $tokenOut = $tokens[$token]['address'];

        $price = $this->getPrice($amountIn, $tokenIn, $tokenOut);

        //$price = number_format($price * pow(10, -$decimalOut), $decimalOut, '.','');

        return $price;
    }


    function getPriceUsdtToBtc($amount)
    {
        $decimalUsdt = self::$decimalUsdt;
        $amountIn = $amount * pow(10, $decimalUsdt);
        $decimalBtc = self::$decimalBtc;

        $tokenIn = self::$tokenUsdt;
        $tokenOut = self::$tokenBtc;

        $price = $this->getPrice($amountIn, $tokenIn, $tokenOut);

        $price = number_format($price * pow(10, -$decimalBtc), $decimalBtc, '.','');
        return $price;
    }


    function getPriceUsdtToUsdt($amount)
    {
        return 1;
    }

}
