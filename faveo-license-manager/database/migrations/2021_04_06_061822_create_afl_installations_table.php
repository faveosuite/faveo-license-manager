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
            $table->primary('installation_id');
            $table->increments('installation_id')->unique();

            $table->foreignId('product_id')->unique()
                  ->constrained('afl_product_id','product_id')
                  ->onDelete('cascade');

            $table->foreignId('client_id')->default('null')
                  ->constrained('afl_clients','client_id')
                  ->onDelete('cascade');

            $table->string('license_code',125)->default('null');
            $table->string('installation_ip',125)->unique();
            $table->string('installation_domain',125)->unique();
            $table->boolean('installation_disable_ip_verification');
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
