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
        Schema::table('afl_api_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('afl_api_keys', 'api_key_versions_add')) {
                $table->boolean('api_key_versions_add')->default(1);
            }
            if (!Schema::hasColumn('afl_api_keys', 'api_key_versions_edit')) {
                $table->boolean('api_key_versions_edit')->default(1);
            }
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
            // Fix the table name in the hasColumn check
            if (Schema::hasColumn('afl_api_keys', 'api_key_versions_add')) {
                $table->dropColumn('api_key_versions_add');
            } 
            if (Schema::hasColumn('afl_api_keys', 'api_key_versions_edit')) {
                $table->dropColumn('api_key_versions_edit');
            } 
        });
    }
};
