<?php

use JustSwap\SwapKit;
use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\Trade;

class TronTrade extends Address
{
    const PRIVATE_KEY = '38ecbc853bea64ca46a825bb7e67b6edc4b779756f45fb3637c9434d2f1e0e6c';

    private $kit;

    private static $instance;

    private array $tokens;

    public function __construct($config = [])
    {
        $this->kit = new SwapKit(
            TronApi::mainNet(),
            Credential::fromPrivateKey(self::PRIVATE_KEY),
            self::$factory
        );

        $this->tokens = self::getTokens();

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function balance()
    {
        $balanceUsdt = $this->balanceUsdt();
        $balanceEth = $this->balanceEth();
        $balanceBtc = $this->balanceBtc();

        $format = "💰 Balance: USDT - $balanceUsdt, ETH:$balanceEth, BTC - $balanceBtc";
        echo $format, PHP_EOL;
    }

    private static $item = 0;

    private static $sum = 0;

    public function init1()
    {
        $this->balance();
        if(self::$item == 2){

            /**
             * ETH - 0.020468785631167
             * USDT - 8.108188
             * BTC - 0.0004168
             * 0.004 ETH -> 6.703823 USDT
             *
             */

            $trade = $this->kit->getTradeBuilder()
                ->tradeType(Trade::EXACT_INPUT)
                ->tokenIn('THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF') // ETH - 0,01646878563
                ->tokenOut('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t') // USDT - 14,822078
                ->amountIn(bn('4000000000000000')) // 0.004 ETH
                ->slippage(bn('10'))
                ->to($this->kit->getDefaultAddress())
                ->build();
            $txId = $this->kit->executeTrade($trade);

            echo "🔥 " . $txId, PHP_EOL;
        }
        //TRX->USDT

//        exit;

        $this->balance();

        echo ++self::$item . " >-------", PHP_EOL;
        return;

    }

    public function init()
    {
        $balanceUsdt = $this->balanceUsdt();

echo $balanceUsdt;
        exit;
//        $trade = $this->kit->getTradeBuilder()
//            ->tradeType(Trade::EXACT_INPUT)
//            ->tokenIn('THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF')
//            ->tokenOut('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t')
//            ->amountIn(bn(39635229547311))
//            ->slippage(bn('10'))
//            ->to($this->kit->getDefaultAddress())
//            ->build();
//
//        $txId = $this->kit->executeTrade($trade);
//
//        echo 'txid => ' . $txId . PHP_EOL;
//        echo 'waiting for confirmation...' . PHP_EOL;
//        $success = $this->kit->waitForConfirmation($txId);
//        echo "🔥 " . 'success => ' . $success . PHP_EOL;
//
//        exit;


        // Determination of balances
        $balanceUsdt = $this->balanceUsdt();
        $balanceEth = $this->balanceEth();
        $balanceBtc = $this->balanceBtc();
        $balanceBtcUsdt = $this->getPriceBtcToUsdt($balanceBtc);
        $balanceEthUsdt = $this->getPriceEthToUsdt($balanceEth);

        $format = "💰 Balance: USDT - $balanceUsdt ($balanceUsdt USDT), ETH:$balanceEth ($balanceEthUsdt USDT), BTC - $balanceBtc ($balanceBtcUsdt USDT)";

        $sum = $balanceUsdt + $balanceEthUsdt + $balanceBtcUsdt ;

        if(self::$sum == 0 ){
            self::$sum = $sum;
        }
        echo $format . " => 💖 $sum USDT" . "(".self::$sum.")", PHP_EOL;

        //$format = "📈 BTC->USDT: $balanceBtcUsdt, ETH->USDT: $balanceEthUsdt, USDT: $balanceUsdt";

        $arr = [];

        $arr['BTC_ETH'] = $this->getIncrement('BTC', 'ETH', $balanceBtcUsdt, $balanceEthUsdt);
        $arr['BTC_USDT'] = $this->getIncrement('BTC', 'USDT', $balanceBtcUsdt, $balanceUsdt);
        $arr['ETH_USDT'] = $this->getIncrement('ETH', 'USDT', $balanceEthUsdt, $balanceUsdt);

        $str = ''; $maxIncr = 0; $maxDelta = '';

        foreach ($arr as $k=>$data){
            if($maxIncr < $data['incr']) {
                $maxIncr = $data['incr'];
                $maxKey = $k;
                $maxDelta = $data['delta'];
            }
            $str .= $k . ": " . $data['incr'] . "%". "(Δ".$data['delta']."), ";
        }

        $strMax = "MAX: " . $maxKey ." - ". $maxIncr ."% " . "(Δ".$maxDelta.")";

        echo "📈 " . $str . $strMax, PHP_EOL;


        $arr1 = $arr[$maxKey];

        switch ($maxKey) {
            case 'BTC_ETH':
                $balance1 = $balanceBtc;
                $balance2 = $balanceEth;
                break;
            case 'BTC_USDT':
                $balance1 = $balanceBtc;
                $balance2 = $balanceUsdt;
                break;
            case 'ETH_USDT':
                $balance1 = $balanceEth;
                $balance2 = $balanceBtc;
                break;
        }

        $limit = 0.3;

       // if($maxIncr > $limit && $sum > self::$sum){
            self::$sum = $sum;
            $this->swap($arr[$maxKey], $balance1, $balance2);
            //}

        echo "-------", PHP_EOL;

    }

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

        echo $rebalanceUsdt . " USDT / " . $amountTokenIn . " " . $token . " -> " . $tokenOut, PHP_EOL;

        //print_r($arr);


//        echo $tokenAddressIn, PHP_EOL;
//        echo $tokenAddressOut, PHP_EOL;
//        echo $amountTokenIn, PHP_EOL;

       // echo $this->kit->getToken($tokenAddressIn)->symbol() . "->" . $this->kit->getToken($tokenAddressOut)->symbol(), PHP_EOL;

        echo $tokenAddressIn . " -> " . $tokenAddressOut . " / " . $amountTokenIn, PHP_EOL;
        $trade = $this->kit->getTradeBuilder()
            ->tradeType(Trade::EXACT_INPUT)
            ->tokenIn($tokenAddressIn)
            ->tokenOut($tokenAddressOut)
            ->amountIn(bn($amountTokenIn))
            ->slippage(bn('10'))
            ->to($this->kit->getDefaultAddress())
            ->build();

        $txId = $this->kit->executeTrade($trade);

        echo 'txid => ' . $txId . PHP_EOL;
        echo 'waiting for confirmation...' . PHP_EOL;
        $success = $this->kit->waitForConfirmation($txId);
        echo "🔥 " . 'success => ' . $success . PHP_EOL;

    }

    function getPriceUsdtToToken($amountUsdt,$token)
    {
        $tokens = $this->tokens;

        $decimalIn = $tokens['USDT']['decimal'];
        $amountToken = number_format($amountUsdt * pow(10, $decimalIn), $decimalIn, '.','');
        $decimalOut = $tokens[$token]['decimal'];

        $tokenAddressIn = $tokens['USDT']['address'];
        $tokenAddressOut = $tokens[$token]['address'];

        $price = (int)$this->kit->getAmountOut(bn($amountToken), $tokenAddressIn, $tokenAddressOut)->__toString();

        //$price = number_format($price * pow(10, -$decimalOut), $decimalOut, '.','');

        return $price;
    }

    public function getIncrement($token1,$token2,$balance1,$balance2){

        if($balance2 > $balance1){
            $ratio = $balance1 / $balance2 ;
            $direct = 1;
            $delta = $balance2 - $balance1;
        }
        else{
            $ratio = $balance2 / $balance1;
            $direct = -1;
            $delta = $balance1 - $balance2;
        }
        $delta = round($delta,2,PHP_ROUND_HALF_UP);
        $incr = 100*(1-$ratio) ;
        $incr = round($incr,3, PHP_ROUND_HALF_UP) ;
        return [
            'token1'=>$token1,
            'token2'=>$token2,
            'incr'=>$incr,
            'direct'=>$direct,
            'delta'=>$delta,
            'balance1'=>$balance1,
            'balance2'=>$balance2
        ];
    }

    public function balanceUsdt()
    {
        $balance = (int)$this->kit->getToken(self::$tokenUsdt)->call('balanceOf', self::$account)->value;
        $decimal = self::$decimalUsdt;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }

    public function balanceBtc()
    {
        $balance = (int)$this->kit->getToken(self::$tokenBtc)->call('balanceOf', self::$account)->value;
        $decimal = self::$decimalBtc;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }

    public function balanceEth()
    {
        $balance = (int)$this->kit->getToken(self::$tokenEth)->call('balanceOf', self::$account)->value;
        $decimal = self::$decimalEth;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }

    public function balanceTrx()
    {
        $balance = (int)$this->kit->getTrxBalance(self::$account);
        $decimal = self::$decimalTrx;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }

    function getPriceEthToUsdt($amount)
    {
        $decimalEth = self::$decimalEth;
        $amountToken = number_format($amount * pow(10, $decimalEth), $decimalEth, '.','');
        $decimalUsdt = self::$decimalUsdt;
        $price = (int)$this->kit->getAmountOut(bn($amountToken), self::$tokenEth, self::$tokenUsdt)->__toString();
        $price = number_format($price * pow(10, -$decimalUsdt), $decimalUsdt, '.','');
        return $price;
    }

    function getPriceBtcToUsdt($amount)
    {
        $decimalBtc = self::$decimalBtc;
        $amountToken = $amount * pow(10, $decimalBtc);
        $decimalUsdt = self::$decimalUsdt;
        $price = (int)$this->kit->getAmountOut(bn($amountToken), self::$tokenBtc, self::$tokenUsdt)->__toString();
        $price = number_format($price * pow(10, -$decimalUsdt), $decimalUsdt, '.','');
        return $price;
    }

    function getPriceUsdtToBtc($amount)
    {
        $decimalUsdt = self::$decimalUsdt;
        $amountToken = $amount * pow(10, $decimalUsdt);
        $decimalBtc = self::$decimalBtc;
        $price = (int)$this->kit->getAmountOut(bn($amountToken), self::$tokenUsdt, self::$tokenBtc)->__toString();
        $price = number_format($price * pow(10, -$decimalBtc), $decimalBtc, '.','');
        return $price;
    }

    function getPriceUsdtToUsdt($amount)
    {
        return 1;
    }

    public function approve(){
        $tokens = ['THb4CqiFdwNHsWsQCs4JhzwjMWys4aqCbF', 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t','TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9'];
        foreach($tokens as $addr) {

            $token = $this->kit->getToken($addr);
            $id = $token->symbol();
            echo '>>process token ' . $id . '...' . PHP_EOL;

            $exchangeAddr = $this->kit->getFactory()->getExchange($addr);
            if($exchangeAddr == SwapKit::ZERO_ADDRESS){
                die('exchange not exists: ' . $id . PHP_EOL);
            }
            echo 'exchange address => ' . $exchangeAddr . PHP_EOL;

            $allowance = $token->allowance($this->kit->getDefaultAddress(), $exchangeAddr);
            if($allowance->compare(bn(0)) == 0 ){
                echo  'approve now...' . PHP_EOL;
                $txid = $token->approve($exchangeAddr, SwapKit::I256_UNLIMITED, []);
                echo 'txid => ' . $txid . PHP_EOL;
                echo 'waiting for confirmation...' . PHP_EOL;
                $success = $this->kit->waitForConfirmation($txid);
                echo 'success => ' . $success . PHP_EOL;
            }else{
                echo 'allowance: ' . $allowance . PHP_EOL;
            }
        }
        exit;
    }

    private static function getTokens(){
        $tokens['BTC'] = ['address'=>self::$tokenBtc, 'decimal'=>self::$decimalBtc];
        $tokens['ETH'] = ['address'=>self::$tokenEth, 'decimal'=>self::$decimalEth];
        $tokens['USDT'] = ['address'=>self::$tokenUsdt, 'decimal'=>self::$decimalUsdt];
        return $tokens;
    }

}




