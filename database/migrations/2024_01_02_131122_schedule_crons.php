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
        Schema::create('schedule_crons', function (Blueprint $table) {
            $table->id();
            $table->string('scenario')->unique();
            $table->string('value');
            $table->string('command')->unique();
            $table->integer('status')->default(0);
            $table->string('job_info')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_crons');
    }
};
