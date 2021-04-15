<?php


namespace App\Library\Traits;


trait BalanceTrait
{

    public function balance()
    {
        $balanceUsdt = $this->balanceUsdt();
        $balanceEth = $this->balanceEth();
        $balanceBtc = $this->balanceBtc();

        $format = "💰 Balance: USDT - $balanceUsdt, ETH:$balanceEth, BTC - $balanceBtc";
        echo $format, PHP_EOL;
    }

    public function getBalance($tokenAddr)
    {
        try {
            $balance = (int)$this->kit->getToken($tokenAddr)->call('balanceOf', self::$account)->value;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            echo $e->getResponse()->getStatusCode() . " - " . $tokenAddr . " - " . PHP_EOL;
            $balance = 0;
        }
        return $balance;
    }

    public function balanceUsdt()
    {
        $tokenAddr = self::$tokenUsdt;
        $balance = $this->getBalance($tokenAddr);
        $decimal = self::$decimalUsdt;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }

    public function balanceBtc()
    {
        $tokenAddr = self::$tokenBtc;
        $balance = $this->getBalance($tokenAddr);
        $decimal = self::$decimalBtc;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }

    public function balanceEth()
    {
        $tokenAddr = self::$tokenEth;
        $balance = $this->getBalance($tokenAddr);
        $decimal = self::$decimalEth;
        $balance = $balance * pow(10, -$decimal);
        return $balance;
    }


    public function balanceTrx()
    {
        try {
            $balance = (int)$this->kit->getTrxBalance(self::$account);
            $decimal = self::$decimalTrx;
            $balance = $balance * pow(10, -$decimal);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            echo $e->getResponse()->getStatusCode() . " - " . self::$account . PHP_EOL;
            $balance = 0;
        }

        return $balance;
    }

}
