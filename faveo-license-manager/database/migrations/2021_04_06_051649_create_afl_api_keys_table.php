<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_api_keys', function (Blueprint $table) {
            $table->primary('api_key_id');
            $table->increments('api_key_id')->unique();
            $table->string('api_key_secret',125)->unique();
            $table->string('api_key-ip',125);
            $table->tinyInteger('api_key_clients_add');
            $table->tinyInteger('api_key_clients_edit');
            $table->tinyInteger('api_key_licenses_add');
            $table->tinyInteger('api_key_licenses_edit');
            $table->tinyInteger('api_key_products_add');
            $table->tinyInteger('api_key_products_edit');
            $table->tinyInteger('api_key_installations_edit');
            $table->tinyInteger('api_key_search');
            $table->boolean('api_key_status');


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
        Schema::dropIfExists('afl_api_keys');
    }
}
