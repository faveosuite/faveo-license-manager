<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfuFailedUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afu_failed_updates', function (Blueprint $table) {
            $table->increments('failed_update_id');
            $table->string('failed_update_ip', 125);
            $table->mediumInteger('failed_update_attempts');
            $table->date('failed_update_last_attempt_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afu_failed_updates');
    }
}
