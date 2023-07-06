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
        Schema::table('afl_callbacks', function (Blueprint $table) {
        $table->index('license_code');
        });
        
        Schema::table('afl_licenses', function (Blueprint $table) {
        $table->index('product_id');
        });
        Schema::table('afl_licenses', function (Blueprint $table) {
        $table->index('license_code');
        });
        Schema::table('afl_callbacks', function (Blueprint $table) {
        $table->index('callback_date_time');
        });
        }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
