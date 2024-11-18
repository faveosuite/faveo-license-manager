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
        Schema::table('directory', function (Blueprint $table) {
            $table->string('disk');
            $table->string('s3_bucket')->nullable();
            $table->string('s3_region')->nullable();
            $table->string('s3_access_key')->nullable();
            $table->string('s3_secret_key')->nullable();
            $table->string('s3_endpoint_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('directory', function (Blueprint $table) {
            $table->dropColumn([
                'disk',
                's3_bucket',
                's3_region',
                's3_access_key',
                's3_secret_key',
                's3_endpoint_url',
            ]);
        });
    }
};
