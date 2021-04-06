<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_profiles', function (Blueprint $table) {
            $table->primary('SETTING_ID');
            $table->foreignId('SETTING_ID');
            $table->string('ROOT_URL',250);
            $table->string('CLIENT_EMAIL',250);
            $table->string('LICENSE_CODE',250);
            $table->string('LCD',250);
            $table->string('LRD',250);
            $table->string('INSTALLATION_KEY',250);
            $table->string('INSTALLATION_HASH',250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afl_profiles');
    }
}
