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
            $table->string('ROOT_URL',125);
            $table->string('CLIENT_EMAIL',125);
            $table->string('LICENSE_CODE',125);
            $table->string('INSTALLATION_HASH',125);
            $table->string('SYSTEM_LANGUAGE',125);
            $table->string('TIMEZONE',125);
            $table->smallInteger('RECORDE_ARCHIVE_DAYS');
            $table->smallInteger('RECORDE_ON_ADMIN_PAGE');
            $table->smallInteger('RECORDE_ON_INDEX_PAGE');
            $table->smallInteger('RECORDE_ON_SEARCH_PAGE');
            $table->boolean('API_STATUS');
            $table->boolean('BANNED_HOSTS');
            $table->string('BANNED_HOST_MESSAGE',125);
            $table->tinyInteger('FAILED_LOGINS_LIMIT');
            $table->tinyInteger('FAILED_LICENSINGS_LIMIT');
            $table->smallInteger('FAILED_HOSTS_FORGET');
            $table->tinyInteger('SMART_REPORTS');
            $table->tinyInteger('SMART_TABLES');
            $table->tinyInteger('MIN_PASSWORD_LENGTH');
            $table->tinyInteger('WHITELISTED_ACCESS');
            $table->string('WHITELISTED_IP',250);
            $table->string('EMAIL_FROM_NAME',125);
            $table->string('EMAIL_FROM_ADDRESS',125);
            $table->tinyInteger('EMAIL_CC_SENDER');
            $table->smallInteger('EMAIL_EXPIRING_LICENSE_DAYS');
            $table->smallInteger('EMAIL_EXPIRING_UPDATES_DAYS');
            $table->smallInteger('EMAIL_EXPIRING_SUPPORT_DAYS');
            $table->date('EXPIRATION_CHECK_DATE');
            $table->boolean('DATABASE_CLEANUP_ENABLED');
            $table->smallInteger('DATABASE_CLEANUP_CALLBACKS');
            $table->smallInteger('DATABASE_CLEANUP_REPORTS_MAIN');
            $table->smallInteger('DATABASE_CLEANUP_REPORTS_SYSTEM');
            $table->smallInteger('DATABASE_CLEANUP_REPORTS_LICENSES');
            $table->date('DATABASE_CLEANUP_DATE');
            $table->longText('NEWS_TEXT');
            $table->date('NEWS_DATE');
            $table->string('ENVATO_API_TOKEN',125);
            $table->string('DATABASE_VERSION',125);
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
