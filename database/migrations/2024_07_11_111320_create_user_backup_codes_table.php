<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_backup_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id')
                ->constrained('users', 'client_id')
                ->onDelete('cascade');
            $table->string('backup_codes');
            $table->timestamps();
            $table->index(['client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_backup_codes');
    }
};
