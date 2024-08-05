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
            $table->integer('version_id')
                ->constrained('afu_versions', 'version_id')
                ->onDelete('cascade');
            $table->string('installation_ip', 125);
            $table->string('installation_domain')->constrained('afl_installations', 'installation_domain')->onDelete('cascade');
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
