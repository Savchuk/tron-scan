<?php

namespace App\Console\Commands;

use App\Library\LoggerTrade;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Library\TronTrade;

use Illuminate\Support\Facades\Log;

class TronScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tron:scan {along?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $arg = $this->argument('along');

        //LoggerTrade::sendBot($arg);

        $instance = TronTrade::getInstance();

        if($arg == 'along') {
            $i = 0;

            $instance->init();

            while (true) {
                ++$i;

                $instance->init();

                if($i==4){
                    break;
                }
            }
            return 0;
        }

        $instance->init();

        return 0;
    }
}
