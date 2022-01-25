<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfuProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afu_products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->string('product_title', 125);
            $table->string('product_sku', 125)->unique();
            $table->string('product_short_description',125)->nullable();
            $table->string('product_full_description',250)->nullable();
            $table->string('product_url_homepage', 125)->nullable();
            $table->string('product_url_order')->nullable();
            $table->float('product_price')->nullable();
            $table->string('product_key',125)->nullable();
            $table->smallInteger('product_max_active_versions')->nullable();

            $table->date('product_date');
            $table->boolean('product_status');
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
        Schema::dropIfExists('afu_products');
    }
}
