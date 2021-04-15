<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('started')->nullable()->default(false);
            $table->boolean('started_swap')->nullable()->default(false);
            $table->boolean('active')->nullable()->default(false);
            $table->timestamps();
        });

        $item = ['started'=> 0, 'started_swap'=> 1, 'active'=> 0];
        $datetime = \Carbon\Carbon::now()->timezone('Europe/Kiev');
        DB::table('settings')->insert(array(
            'started'=>$item['started'],
            'started_swap'=>$item['started_swap'],
            'active'=> $item['active'],
            'created_at'=> $datetime,
            'updated_at'=> $datetime,
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
