<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_products', function (Blueprint $table) {
            //$table->primary('product_id');
            $table->increments('product_id')->unique();
            $table->string('product_title', 125)->unique();
            $table->string('product_description', 250);
            $table->string('product_sku', 125)->unique();
            $table->string('product_url_homepage', 125);
            $table->string('product_url_download', 125);
            $table->date('product_date');
            $table->string('product_version', 125);
            $table->integer('product_envato_id');
            $table->boolean('product_status');
            $table->timestamps();
        });

    }
    /**
     * Reverse the migrations.
     *  `
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afl_products');
    }
}
