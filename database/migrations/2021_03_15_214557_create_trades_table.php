<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });

        $items = array(
            array(
                'title'=>'Starter Plan',
                'body'=>'Text Starter Plan',
            ),
            array(
                'title'=>'Pro Plan',
                'body'=>'Text Pro Plan',
            ),
            array(
                'title'=>'VIP Plan',
                'body'=>'Text VIP Plan',
            ),
        );

        foreach($items as $k=>$item){
            $datetime = \Carbon\Carbon::now()->addSeconds($k);
            DB::table('trades')->insert(array(
                'title'=>$item['title'],
                'body'=>$item['body'],
                'created_at'=> $datetime,
                'updated_at'=> $datetime,
            ));
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
