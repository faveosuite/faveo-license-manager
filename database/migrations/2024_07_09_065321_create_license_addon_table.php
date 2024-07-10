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
        Schema::create('license_addon', function (Blueprint $table) {
            $table->id();

            $table->mediumInteger('lic_id')->nullable();
            $table->mediumInteger('prod_id')->nullable();
            $table->json('product_attributes_license')->nullable();

            $table->foreign('lic_id')->references('license_id')->on('afl_licenses')->onDelete('cascade');
            $table->foreign('prod_id')->references('product_id')->on('afl_products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_addon');
    }
};
