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
            //$table->primary('callback_id');
            $table->increments('callback_id')->unique();

            $table->integer('product_id')
                ->constrained('afl_products', 'product_id')
                ->onDelete('cascade');

            $table->string('client_id')->default('null')
                ->constrained('afl_clients', 'client_id')
                ->onDelete('cascade');

            $table->string('license_code', 125)->default('null');
            $table->string('callback_ip', 125);
            $table->string('callback_domain', 125);
            $table->dateTime('callback_date_time');
            $table->boolean('callback_status');
            $table->timestamps();
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
