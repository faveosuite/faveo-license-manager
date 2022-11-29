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
        Schema::create('afl_license_schemes', function (Blueprint $table) {
            //$table->primary('scheme_id');
            $table->increments('scheme_id')->unique();
            $table->text('scheme_query');
            $table->boolean('scheme_status');
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
        Schema::dropIfExists('afl_license_schemes');
    }
};
