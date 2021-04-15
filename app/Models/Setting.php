<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['started', 'started_swap', 'active', 'updated_at'];

    public  static function active()
    {
        $setting = Setting::find(1);
        $setting->update(['active' => 1]);
        $setting->touch();
        return $setting;
    }
    public  static function inActive()
    {
        $setting = Setting::find(1);
        $setting->update(['active' => 0, 'started_swap' => 0]);
        $setting->touch();
        return $setting;
    }


    public  static function swapActive()
    {
        $setting = Setting::find(1);
        $setting->update(['started_swap' => 1]);
        $setting->touch();
        return $setting;
    }

    public  static function swapInActive()
    {
        $setting = Setting::find(1);
        $setting->update(['started_swap' => 0]);
        $setting->touch();
        return $setting;
    }



    private static function getDifference($updated_at){
        $time_updated_at = $updated_at;
        $updated_at = new Carbon($time_updated_at );
        $now = Carbon::now();
        $difference = $updated_at->diffInSeconds($now);
        return $difference;
    }

    // Разрешение на свап
    public static function isSwaped(){

        $setting = Setting::find(1);

        if(!$setting->started_swap){
            return false ;
        }
        else{
            return true;
        }
    }

    public static function isStarted(){

        $setting = Setting::find(1);

        // Разрешение на старт
        if(!$setting->active){
            return true;
        }

        if($setting->started == 1 ){
            $diffInSeconds = self::getDifference($setting->updated_at);

            if($diffInSeconds > 120){
                $setting::signEnd();
                $setting::signStart();
                return false;
            }
            return true;
        }
        $setting::signStart();
        return false;
    }

    public static function signStart(){
        $setting = Setting::find(1);
        if($setting->started == 1 ){
            return false;
        }
        $setting->update(['started' => 1]);
        $setting->touch();
        return $setting;
    }

    public static function signEnd(){
        $setting = Setting::find(1);
        $setting->update(['started' => 0]);
        $setting->touch();
        return $setting;
    }
}
