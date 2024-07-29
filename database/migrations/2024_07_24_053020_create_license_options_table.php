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
        Schema::create('license_options', function (Blueprint $table) {

            $table->id();
            $table->mediumInteger('license_id');
            $table->mediumInteger('product_id');
            $table->string('option_group',255);
            $table->string('option_name',255);
            $table->string('key', 255);
            $table->string('value', 255);

            $table->foreign('license_id')->references('license_id')->on('afl_licenses')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('afl_products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_options');
    }
};
