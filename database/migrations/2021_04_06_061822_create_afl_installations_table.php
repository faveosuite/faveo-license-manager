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
            //$table->primary('installation_id');
            $table->increments('installation_id')->unique();

            $table->integer('product_id')->unique()
                  ->constrained('afl_products','product_id')
                  ->onDelete('cascade');

            $table->integer('client_id')
                  ->constrained('afl_clients','client_id')
                  ->onDelete('cascade');

            $table->string('license_code',125)->unique();
            $table->string('installation_ip',125);
            $table->string('installation_domain',125)->default('null');
            $table->boolean('installation_disable_ip_verification')->default(false)->nullable();
            $table->date('installation_date');
            $table->boolean('installation_status');
            $table->string('installation_hash',125);
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
        Schema::dropIfExists('afl_installations');
    }
}
