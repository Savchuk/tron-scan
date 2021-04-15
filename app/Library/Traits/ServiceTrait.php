<?php


namespace App\Library\Traits;


trait ServiceTrait
{
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
