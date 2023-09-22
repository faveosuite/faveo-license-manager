<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_clients', function (Blueprint $table) {
            //$table->primary('client_id');
            $table->increments('client_id')->unique();
            $table->string('client_fname', 125);
            $table->string('client_lname', 125);
            $table->string('client_email', 125)->unique();
            $table->string('client_password', 125)->nullable();
            $table->string('client_role', 125)->default('client');
            $table->date('client_active_date')->nullable();
            $table->date('client_cancel_date')->nullable();
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
};
