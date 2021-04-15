<?php
/* расчитать комиссию которую берет justswap за обмен.
0.3%? Или меньше.
На uniswap 0.3%.
Только не путать с комсой сети трон.
Или может где написано
*/

use JustSwap\SwapKit;
use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\Trade;

class TronTrade extends Address {

    const PRIVATE_KEY = '38ecbc853bea64ca46a825bb7e67b6edc4b779756f45fb3637c9434d2f1e0e6c';

    private SwapKit $kit;

    private static $instance;

    public function __construct($config = []) {

        $this->kit = new SwapKit(
            TronApi::mainNet(),
            Credential::fromPrivateKey(self::PRIVATE_KEY),
            self::$factory,
        );

       //echo $this->kit->getDefaultAddress() . PHP_EOL;

    }

  public static function getInstance()
  {
    if ( is_null( self::$instance ) )
    {
      self::$instance = new self();
    }
    return self::$instance;
  }

    public function balanceTrx() {

        $balance = (int)$this->kit->getTrxBalance(self::$account);
        $decimal = self::$decimalTrx;
        $balance = $balance * pow(10,-$decimal);

        return $balance;
    }

}

//$balance = TronTrade::getInstance()->balance();
//echo $balance . PHP_EOL;






/*
while (true) {

    $balance1 = (int)$kit->getTrxBalance($addressAcc);
    $decimal = 6;
    $balance = $balance1 * pow(10,-$decimal);
    echo $balance . PHP_EOL;
    $amountTrx = 205;

    $priceBtc = getPriceTrxToToken($kit,$tokenBtc,100);
    $priceEth = getPriceTrxToToken($kit,$tokenEth,100);
    $priceUsdt = getPriceTrxToToken($kit,$tokenUsdt,100);

    echo $amountTrx . " TRX->BTC: " . $priceBtc . PHP_EOL;
    echo $amountTrx . " TRX->ETH: " . $priceEth . PHP_EOL;
    echo $amountTrx . " TRX->USDT: " . $priceUsdt . PHP_EOL;

    echo PHP_EOL;

    $amountUsdt = 10;

    $priceBtc = getPriceUsdtToToken($kit,$tokenBtc,$amountUsdt);
    $priceEth = getPriceUsdtToToken($kit,$tokenEth,$amountUsdt);
    $priceUsdt = getPriceUsdtToToken($kit,$tokenUsdt,$amountUsdt);

    echo $amountUsdt . " USDT->BTC: " . $priceBtc . PHP_EOL;
    echo $amountUsdt . " USDT->ETH: " . $priceEth . PHP_EOL;
    echo $amountUsdt . " USDT->USDT: " . $priceEth . PHP_EOL;

    echo PHP_EOL;

    $amountBtc = 1;
    $priceBtc = getPriceTokenToUsdt($kit,$tokenBtc,$amountBtc);
    $priceEth = getPriceTokenToUsdt($kit,$tokenEth,$amountBtc);
    $priceUsdt = getPriceTokenToUsdt($kit,$tokenUsdt,$amountBtc);

    echo $amountBtc . " BTC->USDT: " . $priceBtc . PHP_EOL;
    echo $amountBtc . " ETH->USDT: " . $priceEth . PHP_EOL;
    echo $amountBtc . " USDT->USDT: " . $priceUsdt . PHP_EOL;

    echo PHP_EOL;

    $amountBtc = 10;

    $priceBtc = getPriceTokenToTrx($kit,$tokenBtc,$amountBtc);
    $priceEth = getPriceTokenToTrx($kit,$tokenEth,$amountBtc);
    $priceUsdt = getPriceTokenToTrx($kit,$tokenUsdt,$amountBtc);

    echo $amountBtc . " BTC->TRX: " . $priceBtc . PHP_EOL;
    echo $amountBtc . " ETH->TRX: " . $priceEth . PHP_EOL;
    echo $amountBtc . " USDT->TRX: " . $priceUsdt . PHP_EOL;


    echo "-------" . PHP_EOL;


    sleep(5);

}
*/

function getPriceTokenToTrx($kit,$token,$amount){

    $decimals = (int)$kit->getToken($token)->decimals()->__toString();

    $amountToken = $amount * pow(10,$decimals);

    $tokenTrx = $kit::TRX_TOKEN;

    $decimal = 6;

    $price = (int)$kit->getAmountOut(bn($amountToken),$token,$tokenTrx)->__toString();
    $price =  number_format($price*pow(10, -$decimal),$decimal);

    return $price;
}


function getPriceTokenToUsdt($kit,$token,$amount){

    $decimals = (int)$kit->getToken($token)->decimals()->__toString();

    $amountToken = $amount * pow(10,$decimals);

    $tokenUsdt = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';


    $decimal = 6;

    $price = (int)$kit->getAmountOut(bn($amountToken),$token,$tokenUsdt)->__toString();
    $price =  number_format($price*pow(10, -$decimal),$decimal);

    return $price;
}


function getPriceTrxToToken($kit,$token,$amount){
    $decimals = 6;
    $amountTrx = $amount * pow(10,$decimals);
    $tokenTrx = $kit::TRX_TOKEN;

    $decimal = (int)$kit->getToken($token)->decimals()->__toString();
    $price = (int)$kit->getAmountOut(bn($amountTrx),$tokenTrx,$token)->__toString();
    $price =  number_format($price*pow(10, -$decimal),$decimal);

    return $price;
}


function getPriceUsdtToToken($kit,$token,$amount){
    $decimals = 6;
    $amountUsdt = $amount * pow(10,$decimals);
    $tokenUsdt = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    $decimal = (int)$kit->getToken($token)->decimals()->__toString();
    $price = (int)$kit->getAmountOut(bn($amountUsdt),$tokenUsdt,$token)->__toString();
    $price =  number_format($price*pow(10, -$decimal),$decimal);

    return $price;
}


/*

exit;

// Я получил параметр таким кодом:





exit;

$trade = $kit->getTradeBuilder()                     //获取兑换交易生成器
             ->tradeType(Trade::EXACT_INPUT)         //以输入token数量为基准
             ->tokenIn('T9yD14Nj9j7xAB4dbGeiX9h8vMa2GfnLve')                      //输入token
             ->tokenOut('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t')                     //输出token
             ->amountIn(bn('40000000'))  //输入token的数量
             ->slippage(bn('10'))                    //滑点容忍范围1%
             ->to($kit->getDefaultAddress())         //输出token的接收地址
             ->build();


exit;
$txid = $kit->executeTrade($trade);

echo $txid;

// f2ea9aa853ef269becf7639f528444488d3cf84553ffa3f9eea6ea9ed35076f2
*/




