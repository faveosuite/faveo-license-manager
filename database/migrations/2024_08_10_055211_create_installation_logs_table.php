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
        Schema::create('installation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('license_code')->nullable()->index();
            $table->string('version_number')->nullable();
            $table->string('installation_ip', 125);
            $table->string('installation_domain');
            $table->dateTime('installation_last_active_date');
            $table->boolean('installation_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_logs');
    }
};
