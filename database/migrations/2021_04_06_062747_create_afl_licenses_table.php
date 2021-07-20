<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_licenses', function (Blueprint $table) {
            //$table->primary('license_id');
            $table->increments('license_id')->unique();

            $table->integer('product_id')
                ->constrained('afl_products','product_id')
                ->onDelete('cascade');

            $table->integer('client_id')->nullable()
                ->constrained('afl_clients','client_id')
                ->onDelete('cascade');

            $table->string('license_code',125)->nullable();
            $table->string('license_order_number',125)->nullable();
            $table->string('license_ip',125)->nullable();
            $table->string('license_domain',125)->nullable();
            $table->tinyInteger('license_require_domain');
            $table->smallInteger('license_limit')->nullable();
            $table->date('license_date');
            $table->date('license_cancel_date');
            $table->date('license_expire_date')->nullable();
            $table->date('license_expire_email_date')->nullable();
            $table->date('license_updates_date')->nullable();
            $table->date('license_updates_email_date')->nullable();
            $table->date('license_support_date')->nullable();
            $table->date('license_support_email_date')->nullable();
            $table->string('license_comments',250)->nullable();
            $table->tinyInteger('license_envato');
            $table->boolean('license_status');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.

     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afl_licenses');
    }
}
