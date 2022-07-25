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
        Schema::create('afu_installations', function (Blueprint $table) {
            $table->increments('installation_id')->unique();

            $table->integer('product_id')
                ->constrained('afl_products', 'product_id')
                ->onDelete('cascade');

            $table->integer('version_id')
                ->constrained('afu_versions', 'version_id')
                ->onDelete('cascade');
            $table->string('installation_ip', 125);
            $table->string('installation_path', 125)->default('null');

            $table->date('installation_date');
            $table->boolean('installation_status');
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
        Schema::dropIfExists('afu_installations');
    }
};
