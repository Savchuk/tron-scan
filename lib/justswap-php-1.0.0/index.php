<?php


// https://www.justswap.io
// https://www.justswap.io/docs/justswap-interfaces_en.pdf
// https://api.justswap.io/v1/tradepairlist

// USDT - TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t
// WBTC - TXpw8XeWYeTUd4quDskoUqeQPowRh4jY65
// WETH - TXWkP3jLBqRGojUih1ShzNyDaN5Csnebok

// TRX_TOKEN - T9yD14Nj9j7xAB4dbGeiX9h8vMa2GfnLve

// BTC - TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9
// ETH - THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF

// Account - TFvPSKtX7VtNbLuj8mXp962vxnpSiDJ1u6 - $kit->getDefaultAddress()

// TXk8rQSAvPvBBNtqSoY6nCfsXWCSSpTVQF

// Private key 38ecbc853bea64ca46a825bb7e67b6edc4b779756f45fb3637c9434d2f1e0e6c

// Factory Contract - TXk8rQSAvPvBBNtqSoY6nCfsXWCSSpTVQF




/* расчитать комиссию которую берет justswap за обмен. 
0.3%? Или меньше. 
На uniswap 0.3%. 
Только не путать с комсой сети трон. 
Или может где написано
*/

require 'vendor/autoload.php';

//use JustSwap;
use Justswap\JustSwap\SwapKit;
use Justswap\JustSwap\TronApi;
use Justswap\JustSwap\Credential;
use Justswap\JustSwap\Trade;



//$tokenTrx = $kit::TRX_TOKEN;

$tokenUsdt = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
$tokenBtc = 'TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9'; // "0_TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9":{"quote_name":"TRX","base_decimal":"8","quote_symbol":"TRX","base_name":"Bitcoin","base_id":"TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9","price":"948311.265517822060266094","quote_volume":"102070929910853","quote_id":"0","base_volume":"10675050207","base_symbol":"BTC","quote_decimal":"6"}
$tokenEth = 'THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF'; // "0_THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF":{"quote_name":"TRX","base_decimal":"18","quote_symbol":"TRX","base_name":"Ethereum","base_id":"THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF","price":"29598.776298285827864267","quote_volume":"29296148652122","quote_id":"0","base_volume":"9.666148533454214e20","base_symbol":"ETH","quote_decimal":"6"

//$address = $kit->getDefaultAddress();
//echo $address . PHP_EOL;

$addressAcc = 'TFvPSKtX7VtNbLuj8mXp962vxnpSiDJ1u6';



class TronTrade {
        
    private SwapKit $kit;
    
    public function __construct($config = []) {
        
        $kit = new SwapKit(
            TronApi::mainNet(),
            Credential::fromPrivateKey('38ecbc853bea64ca46a825bb7e67b6edc4b779756f45fb3637c9434d2f1e0e6c'),                              
            Addr::$factory,
        );

    }
    
    public function init() {

        $balance1 = (int)$this->kit->getTrxBalance(Addr::$account);
        $decimal = Addr::$decimalUsdt;
        $balance = $balance1 * pow(10,-$decimal);
        
        echo $balance . PHP_EOL;

    }
}


$trade = new TronTrade();
$trade->init();




class Addr {

    public static string    $tokenUsdt   = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    public static int       $decimalUsdt = 6;


    public static string    $tokenBtc   = 'TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9';
    
    public static int       $decimalBtc = 8;
    
    
    public static string    $tokenEth   = 'THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF';
    
    public static int       $decimalEth = 18;


    public static string    $account    = 'TFvPSKtX7VtNbLuj8mXp962vxnpSiDJ1u6';
    

    public static string    $factory    = 'TXk8rQSAvPvBBNtqSoY6nCfsXWCSSpTVQF';
    
}



while (true) {

/*

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
*/

    sleep(5);

}


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





