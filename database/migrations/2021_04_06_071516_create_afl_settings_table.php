<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_settings', function (Blueprint $table) {

            //$table->primary('SETTING_ID');
            $table->increments('SETTING_ID')->unique();
            $table->string('ROOT_URL',125)->nullable();
            $table->string('CLIENT_EMAIL',125)->nullable();
            $table->string('LICENSE_CODE',125)->nullable();
            $table->string('INSTALLATION_HASH',125)->nullable();
            $table->string('SYSTEM_LANGUAGE',125)->nullable();
            $table->string('TIMEZONE',125)->nullable();
            $table->smallInteger('RECORDS_ARCHIVE_DAYS')->nullable();
            $table->smallInteger('RECORDS_ON_ADMIN_PAGE')->nullable();
            $table->smallInteger('RECORDS_ON_INDEX_PAGE')->nullable();
            $table->smallInteger('RECORDS_ON_SEARCH_PAGE')->nullable();
            $table->boolean('API_STATUS')->nullable();
            $table->boolean('BANNED_HOSTS')->nullable();
            $table->string('BANNED_HOST_MESSAGE',125)->nullable();
            $table->tinyInteger('FAILED_LOGINS_LIMIT')->nullable();
            $table->tinyInteger('FAILED_LICENSINGS_LIMIT')->nullable();
            $table->tinyInteger('FAILED_UPDATES_LIMIT')->nullable();
            $table->smallInteger('FAILED_HOSTS_FORGET')->nullable();
            $table->tinyInteger('SMART_REPORTS')->nullable();
            $table->tinyInteger('SMART_TABLES')->nullable();
            $table->tinyInteger('MIN_PASSWORD_LENGTH')->nullable();
            $table->tinyInteger('WHITELISTED_ACCESS')->nullable();
            $table->string('WHITELISTED_IP',250)->nullable();
            $table->string('VERIFIED_UPDATES')->nullable();
            $table->string('EMAIL_FROM_NAME',125)->nullable();
            $table->string('EMAIL_FROM_ADDRESS',125)->nullable();
            $table->tinyInteger('EMAIL_CC_ADMIN')->nullable();
            $table->smallInteger('EMAIL_EXPIRING_LICENSE_DAYS')->nullable();
            $table->smallInteger('EMAIL_EXPIRING_UPDATES_DAYS')->nullable();
            $table->smallInteger('EMAIL_EXPIRING_SUPPORT_DAYS')->nullable();
            $table->date('EXPIRATION_CHECK_DATE')->nullable();
            $table->boolean('DATABASE_CLEANUP_ENABLED')->nullable();
            $table->smallInteger('DATABASE_CLEANUP_CALLBACKS')->nullable();
            $table->smallInteger('DATABASE_CLEANUP_REPORTS_MAIN')->nullable();
            $table->smallInteger('DATABASE_CLEANUP_REPORTS_SYSTEM')->nullable();
            $table->smallInteger('DATABASE_CLEANUP_REPORTS_LICENSES')->nullable();
            $table->smallInteger('DATABASE_CLEANUP_VERSIONS')->nullable();
            $table->tinyInteger('DATABASE_CLEANUP_FILES')->nullable();
            $table->date('DATABASE_CLEANUP_DATE')->nullable();
            $table->longText('NEWS_TEXT')->nullable();
            $table->date('NEWS_DATE')->nullable();
            $table->string('ENVATO_API_TOKEN',125)->nullable();
            $table->string('DATABASE_VERSION',125)->nullable();
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
        Schema::dropIfExists('afl_settings');
    }
}
