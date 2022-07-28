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
        Schema::table('afl_settings', function (Blueprint $table) {
            $table->string('VERIFIED_UPDATES')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('afl_settings', function (Blueprint $table) {
            $table->dropForeign('VERIFIED_UPDATES');
        });
    }
};
