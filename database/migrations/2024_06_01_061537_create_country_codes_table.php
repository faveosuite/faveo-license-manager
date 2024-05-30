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
        Schema::create('country_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('nickname', 100);
            $table->char('iso2', 2);
            $table->char('iso3', 3);
            $table->smallInteger('number_code');
            $table->integer('phone_code');
            $table->string('example');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_codes');
    }
};
