<?php


namespace App\Library\Traits;


trait CalculateTrait
{

    /**
     * Определение приращения
     * @param $token1
     * @param $token2
     * @param $balance1
     * @param $balance2
     * @return array
     */

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

}
