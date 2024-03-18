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
         Schema::create('common_settings', function (Blueprint $table) {
             $table->id();
             $table->string('key')->nullable();
             $table->text('value')->nullable();
             $table->timestamps();
         });
     }
 
     public function down()
     {
         Schema::dropIfExists('common_settings');
     }
};
