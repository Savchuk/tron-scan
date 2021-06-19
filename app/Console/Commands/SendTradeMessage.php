<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTradeMessage extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:message {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send trade message.';

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
        $message = $this->argument('message');
        event(new \App\Events\TradeMessageWasReceived($message));
        //return 0;
    }
}
