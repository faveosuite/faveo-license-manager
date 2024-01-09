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
        Schema::table('users', function (Blueprint $table) {
            $table->date('client_active_date')->default('0000-00-00 ')->nullable(false)->change();
            $table->date('client_cancel_date')->default('0000-00-00	')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('client_active_date')->nullable()->change();
            $table->date('client_cancel_date')->nullable()->change();
        });
    }
};
