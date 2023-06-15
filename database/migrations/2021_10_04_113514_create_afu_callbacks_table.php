<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afu_callbacks', function (Blueprint $table) {
            $table->increments('callback_id')->unique();

            $table->integer('product_id')
                ->constrained('afl_products', 'product_id')
                ->onDelete('cascade');

            $table->string('version_id')->default('null')
                ->constrained('afu_versions', 'version_id')
                ->onDelete('cascade');
            $table->string('callback_type', 125);
            $table->string('callback_ip', 125);
            $table->string('callback_path', 125);
            $table->dateTime('callback_date_time');
            $table->boolean('callback_status');
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
        Schema::dropIfExists('afu_callbacks');
    }
};
