<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflCallbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_callbacks', function (Blueprint $table) {
            $table->primary('callback_id');
            $table->increments('callback_id');
            $table->foreignId('product_id');
            $table->foreignId('client_id')->nullable();
            $table->string('license_code',125)->nullable();
            $table->string('callback_ip',125);
            $table->string('callback_domain',125);
            $table->dateTime('callback_date_time');
            $table->boolean('callback_status');
            $table->timestamps();

            $table->foreign('client_id')
                ->references('client_id')
                ->on('afl_clients')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('product_id')
                ->on('afl_products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afl_callbacks');
    }
}
