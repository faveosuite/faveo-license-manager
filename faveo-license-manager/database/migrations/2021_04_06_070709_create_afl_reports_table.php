<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_reports', function (Blueprint $table) {
            $table->primary('report_id');
            $table->increments('report_id')->unique();
            $table->foreignId('product_id');
            $table->foreignId('account_id')->nullable();
            $table->string('license_code',125)->nullable();
            $table->dateTime('report_date_time');
            $table->string('report_text',5000);
            $table->tinyInteger('report_system');
            $table->boolean('report_status');
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
        Schema::dropIfExists('afl_reports');
    }
}
