<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('afl_licenses', function (Blueprint $table) {
            $table->json('product_attributes_license')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afl_licenses', function (Blueprint $table) {
            $table->dropColumn('product_attributes_license');
        });
    }
};
