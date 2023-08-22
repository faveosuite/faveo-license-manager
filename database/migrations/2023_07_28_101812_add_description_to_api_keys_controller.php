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
        Schema::table('api_keys_controller', function (Blueprint $table) {
            $table->longText('api_key_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('afl_api_keys', function (Blueprint $table) {
            if (Schema::hasColumn('api_keys_controller', 'api_key_description')) {
                $table->dropColumn('api_key_description');
            } 
        });
    }
};
