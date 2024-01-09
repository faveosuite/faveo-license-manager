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
        Schema::table('afl_settings', function (Blueprint $table) {
            $table->string('EMAIL_DRIVER')->nullable();        
            $table->string('EMAIL_PASSWORD', 255)->nullable();  
            $table->integer('EMAIL_PORT')->nullable();  
            $table->string('EMAIL_ENCRYPTION')->nullable();  
            $table->string('EMAIL_HOST')->nullable();  
            $table->boolean('EMAIL_SENDING_STATUS')->default(1);  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afl_settings', function (Blueprint $table) {
            $table->dropColumn('EMAIL_DRIVER');
            $table->dropColumn('EMAIL_PASSWORD');
            $table->dropColumn('EMAIL_PORT');
            $table->dropColumn('EMAIL_ENCRYPTION');
            $table->dropColumn('EMAIL_HOST');
            $table->dropColumn('EMAIL_SENDING_STATUS');
        });
    }
    
};
