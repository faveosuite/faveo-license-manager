<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflAdminSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_admin_sessions', function (Blueprint $table) {
            $table->increments('session_id')->unique();
            $table->string('admin_hash',125);
            $table->string('admin_session_hash',125);
            $table->date('admin_session_date');
            $table->date('admin_session_expiry_date');
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
        Schema::dropIfExists('afl_admin_sessions');
    }
}
