<?php

namespace App\Library;

use App\Library\Address;

use App\Library\Logger;
use Carbon\Carbon;
use JustSwap\SwapKit;
use JustSwap\TronApi;
use JustSwap\Credential;
use JustSwap\Trade;

use App\Library\LoggerTrade;

use \App\Library\Traits\BalanceTrait;
use \App\Library\Traits\PriceTrait;
use \App\Library\Traits\ServiceTrait;
use \App\Library\Traits\SwapTrait;
use \App\Library\Traits\CalculateTrait;

use Symfony\Component\Console\Output\ConsoleOutput;

use \App\Models\Setting;

class TronTrade extends Address
{

    /**
     * Use traits to calucate chains
     */
    use BalanceTrait, PriceTrait, ServiceTrait, SwapTrait, CalculateTrait;

    /**
     * Initialization of SwapKit
     *
     * @var SwapKit
     */
    private $kit;

    /**
     * Execute of chain tron
     *
     * @var
     */
    private static $instance;

    /**
     * Array of tokens
     *
     * @var array
     */
    private $tokens;


    /**
     * Accumulation sum
     *
     * @var int
     */
    private static $sum = 0;

    private static $output;

    public function __construct($config = []) {

        $private_key = env('TRON_PRIVATE_KEY',null);

        $this->kit = new SwapKit(
            TronApi::mainNet(),
            Credential::fromPrivateKey($private_key),
            self::$factory
        );

        $this->tokens = self::getTokens();

        self::$output = new ConsoleOutput();
    }

    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(){

        if(Setting::isStarted()){
            sleep(4);
            return;
        }

        // Determination of balances
        $balanceUsdt =  $this->balanceUsdt();
        sleep(1);
        $balanceEth =   $this->balanceEth();
        sleep(1);
        $balanceBtc =   $this->balanceBtc();
        sleep(1);
        $balanceTrx =   $this->balanceTrx();

        sleep(2);
        $balanceBtcUsdt = $this->getPriceBtcToUsdt($balanceBtc);
        sleep(2);
        $balanceEthUsdt = $this->getPriceEthToUsdt($balanceEth);
        sleep(2);
        $balanceTrxUsdt = $this->getPriceTrxToUsdt($balanceTrx);

        $message = [];

        if($balanceBtcUsdt != 0 && $balanceEthUsdt != 0 && $balanceTrxUsdt != 0){

            $resBalance = $balanceBtcUsdt + $balanceEthUsdt + $balanceUsdt;
            $resBalance = round( $resBalance, 2);

            $balances = [
                'USDT'=> round($balanceUsdt,4),
                'ETH'=> $balanceEth,
                'BTC'=> $balanceBtc,
                'BTCUSDT'=> round($balanceBtcUsdt,4),
                'ETHUSDT'=> round($balanceEthUsdt,4),
                'TRX'=> round($balanceTrx,4),
                'TRXUSDT'=> round($balanceTrxUsdt,4),
            ];
            $data = Carbon::now()->format('Y-m-d H:i:s');

            $message = ['balances'=>$balances, 'data'=>$data, 'res'=> $resBalance];

        }

        $format = "💰 Balance: USDT - $balanceUsdt ($balanceUsdt USDT), ETH:$balanceEth ($balanceEthUsdt USDT), BTC - $balanceBtc ($balanceBtcUsdt USDT)";

        $sum = $balanceUsdt + $balanceEthUsdt + $balanceBtcUsdt ;

        if(self::$sum == 0 ){
            self::$sum = $sum;
        }

        $format = " => 💖 $sum USDT" . "(".self::$sum.")";
        self::$output->writeln($format);

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

        $format = "📈 " . $str . $strMax;
        self::$output->writeln($format);

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

        //$limit = 1;
        $limit = 0.5;

        $message['max_incr'] = round($maxIncr,2);
        $message['limit_incr'] = round($limit,2);

        event(new \App\Events\TradeMessageWasReceived($message));


        $isSwaped = Setting::isSwaped();

        if ($maxIncr > $limit && $sum >= self::$sum && $isSwaped == true) {

            self::$sum = $sum;
            $txId = $this->swap($arr[$maxKey], $balance1, $balance2);
            if($txId){
                self::$output->writeln($txId);

                $api = TronApi::mainNet();
                $trans = $api->getTransactionInfoById($txId);

                $balanceTrxRes =   $this->balanceTrx();

                $fee = $trans->fee;
                $fee_trx = round($fee / 1000000 , 2);

                $swap = new \App\Models\Swap();
                $swap->txid = $txId;
                $swap->direct = $maxKey;
                $swap->fee = $fee;
                $swap->fee_trx = $fee_trx;
                $swap->result = $resBalance;
                $swap->block_number = $trans->blockNumber;
                $swap->block_timestamp = $trans->blockTimeStamp;
                $swap->before_balance_trx = $balanceTrx;
                $swap->after_balance_trx = $balanceTrxRes;

                try {
                    $swap->save();
                } catch (\Exception $e) {
                    echo $e->getCode();
                }
            }
        }

        $format = "-------";
        self::$output->writeln($format);

        sleep(3);

        $end = Setting::signEnd();
    }
}
