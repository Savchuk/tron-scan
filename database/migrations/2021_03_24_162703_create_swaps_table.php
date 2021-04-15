<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSwapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('swaps');

        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->string('txid');
            $table->string('direct');
            $table->bigInteger('fee')->nullable()->default(0);
            $table->decimal('fee_trx', 18, 2)->nullable()->default(0);
            $table->bigInteger('block_number');
            $table->bigInteger('block_timestamp');
            $table->decimal('result', 18, 2)->nullable()->default(0);
            $table->decimal('before_balance_trx', 18, 6)->nullable()->default(0);
            $table->decimal('after_balance_trx',18, 6)->nullable()->default(0);
            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('swaps');
    }
}
