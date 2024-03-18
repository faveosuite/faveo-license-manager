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
        Schema::table('afl_banned_hosts', function (Blueprint $table) {
            $table->string('banned_host_comments', 250)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afl_banned_hosts', function (Blueprint $table) {
            $table->string('banned_host_comments', 250)->nullable(false)->change();
        });
    }
};
