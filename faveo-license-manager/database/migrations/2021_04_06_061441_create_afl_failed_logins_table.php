<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflFailedLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_failed_logins', function (Blueprint $table) {
            $table->increments('failed_login_id');
            $table->string('failed_login_ip',125);
            $table->integer('failed_login_attempts');
            $table->date('failed_login_last_attempt_date');
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
        Schema::dropIfExists('afl_failed_logins');
    }
}
