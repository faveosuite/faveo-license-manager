<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflBannedHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_banned_hosts', function (Blueprint $table) {
            //$table->primary('banned_host_id');
            $table->increments('banned_host_id')->unique();
            $table->string('banned_host_ip',125)->unique();
            $table->string('banned_host_comments',250);
            $table->date('banned_host_date');
            $table->mediumInteger('banned_host_blocks');
            $table->date('banned_host_last_block_date');
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
        Schema::dropIfExists('afl_banned_hosts');
    }
}
