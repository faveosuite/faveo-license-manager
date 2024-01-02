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
        Schema::create('afl_whitelist_ips', function (Blueprint $table) {
            $table->increments('whitelist_host_id')->unique();
            $table->string('whitelist_host_ip', 125)->unique();
            $table->string('whitelist_host_comments', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afl_whitelist_ips');
    }
};
