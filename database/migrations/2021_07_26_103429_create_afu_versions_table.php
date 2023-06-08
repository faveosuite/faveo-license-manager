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
        // Schema::create('afu_versions', function (Blueprint $table) {
        //     $table->increments('version_id')->unique();

        //     $table->integer('product_id')
        //         ->constrained('afl_products', 'product_id')
        //         ->onDelete('cascade');

        //     $table->string('version_number', 125);
        //     $table->string('version_install_file', 125)->nullable();
        //     $table->string('version_install_query', 125)->nullable();
        //     $table->string('version_raw_install_query')->nullable();
        //     $table->string('version_upgrade_file', 125)->nullable();
        //     $table->string('version_upgrade_query', 125)->nullable();
        //     $table->string('version_raw_upgrade_query', 125)->nullable();
        //     $table->mediumInteger('version_install_limit')->nullable();
        //     $table->mediumInteger('version_install_count')->nullable();
        //     $table->mediumInteger('version_upgrade_limit')->nullable();
        //     $table->mediumInteger('version_upgrade_count')->nullable();
        //     $table->string('version_changelog')->nullable();
        //     $table->date('version_date')->nullable();
        //     $table->date('version_expire_date')->nullable();
        //     $table->string('version_comments', 250)->nullable();
        //     $table->boolean('version_status');

        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afu_versions');
    }
};
