<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_notifications', function (Blueprint $table) {
            $table->primary('notifications_id');
            $table->increments('notification_id')->unique();
            $table->string('notification_product_not_found',250);
            $table->string('notification_product_inactive',250);
            $table->string('notification_license_ok',250);
            $table->string('notification_license_not_found',250);
            $table->string('notification_invalid_ip',250);
            $table->string('notification_invalid_domain',250);
            $table->string('notification_domain_required',250);
            $table->string('notification_domain_in_use',250);
            $table->string('notification_license_suspended',250);
            $table->string('notification_license_expired',250);
            $table->string('notification_updates_expired',250);
            $table->string('notification_support_expired',250);
            $table->string('notification_license_cancelled',250);
            $table->string('notification_license_limit',250);
            $table->string('notification_installation_not_found',250);
            $table->string('notification_invalid_signature',250);
            $table->string('notification_host_banned',250);
            $table->string('notification_unknown_error',250);
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
        Schema::dropIfExists('afl_notifications');
    }
}
