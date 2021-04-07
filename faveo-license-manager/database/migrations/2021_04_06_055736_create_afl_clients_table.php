<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_clients', function (Blueprint $table) {
            $table->primary('client_id');
            $table->increments('client_id')->unique();
            $table->string('client_fname',125);
            $table->string('client_lname',125);
            $table->string('client_email',125)->unique();
            $table->date('client_active_date');
            $table->date('client_cancel_date');
            $table->boolean('client_status')->default('1');
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
        Schema::dropIfExists('afl_clients');
    }
}
