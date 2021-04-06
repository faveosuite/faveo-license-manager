<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflInstallationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_installations', function (Blueprint $table) {
            $table->increments('installation_id')->unique();
            $table->foreignId('product_id');
            $table->foreignId('client_id')->nullable();
            $table->string('license_code',125)->nullable();
            $table->string('installation_ip',125);
            $table->string('installation_domain',125);
            $table->boolean('installation_disable_ip_verification');
            $table->date('installation_date');
            $table->boolean('installation_status');
            $table->string('installation_hash',125);
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
        Schema::dropIfExists('afl_installations');
    }
}
