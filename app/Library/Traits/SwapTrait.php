<?php

namespace App\Library\Traits;

use JustSwap\SwapKit;
use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\Trade;

trait SwapTrait
{
    public function swap($arr, $balance1, $balance2) {

        // Definition of tokens
        $tokens = $this->tokens;
        $token1 = $arr['token1'];
        $token2 = $arr['token2'];

        if($arr['direct'] == 1){
            $tokenIn = $token2;
            $tokenOut = $token1;
            $tokenAddressIn = $tokens[$arr['token2']]['address'];
            $tokenAddressOut = $tokens[$arr['token1']]['address'];
        }
        else{
            $tokenIn = $token1;
            $tokenOut = $token2;
            $tokenAddressIn = $tokens[$arr['token1']]['address'];
            $tokenAddressOut = $tokens[$arr['token2']]['address'];
        }

        // Rebalancing calculation
        $rebalanceUsdt = $arr['delta'] * 0.5 ;

        // Getting amount tokenIn
        $amountUsdt = $rebalanceUsdt;
        $token = $tokenIn;
        $amountTokenIn = $this->getPriceUsdtToToken($amountUsdt,$token);

        $txId = '';

        $key = env('TRON_PRIVATE_KEY',null);
        $factory_address = self::$factory;

        $kit = new SwapKit(
            TronApi::mainNet(),
            Credential::fromPrivateKey($key),
            $factory_address
        );

        try {
            $trade = $kit->getTradeBuilder()
                ->tradeType(Trade::EXACT_INPUT)
                ->tokenIn($tokenAddressIn)
                ->tokenOut($tokenAddressOut)
                ->amountIn(bn($amountTokenIn))
                ->slippage(bn('10'))
                ->to($kit->getDefaultAddress())
                ->build();

            $txId = $kit->executeTrade($trade);
            $success = $kit->waitForConfirmation($txId);

        } catch (\GuzzleHttp\Exception\RequestException $e) {

            echo $e->getResponse()->getStatusCode();
            $balance = 0;
        }

        return $txId;

    }
}
