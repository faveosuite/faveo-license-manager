<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_emails', function (Blueprint $table) {
            $table->primary('email_id');
            $table->increments('email_id')->unique();
            $table->string('email_expiring_license_subject',125);
            $table->text('email_expiring_license_text');
            $table->string('email_expiring_updates_subject',125);
            $table->text('email_expiring_updates_text');
            $table->string('email_expiring_support_subject',125);
            $table->text('email_expiring_support_text');
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
        Schema::dropIfExists('afl_emails');
    }
}
