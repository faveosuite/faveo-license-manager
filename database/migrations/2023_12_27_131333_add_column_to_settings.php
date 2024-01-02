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
    { Schema::table('afl_settings', function (Blueprint $table) {
        $table->string('PASSWORD_VALIDATION_MESSAGE', 125)->after('MIN_PASSWORD_LENGTH')->nullable();
        $table->tinyInteger('FAILED_LOGINS')->after('BANNED_HOST_MESSAGE')->nullable();
        $table->tinyInteger('FAILED_FORGET_LIMIT')->after('FAILED_HOSTS_FORGET')->nullable();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afl_settings', function (Blueprint $table) {
           $table->dropColumn('PASSWORD_VALIDATION_MESSAGE');
            $table->dropColumn('FAILED_LOGINS');
            $table->dropColumn('FAILED_FORGET_LIMIT');
        });
    }
    };
