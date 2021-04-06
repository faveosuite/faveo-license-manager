<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflFailedLicensingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_failed_licensings', function (Blueprint $table) {
            $table->increments('failed_licensing_id');
            $table->string('failed_licensing_ip',125);
            $table->integer('failed_licensing_attempts');
            $table->date('failed_licensing_last_attempt_date');
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
        Schema::dropIfExists('afl_failed_licensings');
    }
}
