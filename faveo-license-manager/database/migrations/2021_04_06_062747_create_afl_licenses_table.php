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
            $table->increments('license_id')->unique();
            $table->mediumInteger('product_id');
            $table->mediumInteger('client_id')->nullable();
            $table->string('license_code',125)->nullable();
            $table->string('license_order_number',125);
            $table->string('license_ip',125);
            $table->string('license_domain',125);
            $table->boolean('license_require_domain');
            $table->smallInteger('license_limit');
            $table->date('license_date');
            $table->date('license_cancel_date');
            $table->date('license_expire_date');
            $table->date('license_expire_email_date');
            $table->date('license_updates_date');
            $table->date('license_updates_email_date');
            $table->date('license_support_date');
            $table->date('license_support_email_date');
            $table->string('license_comments',250);
            $table->boolean('license_envato');
            $table->boolean('license_status');
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

     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afl_licenses');
    }
}
