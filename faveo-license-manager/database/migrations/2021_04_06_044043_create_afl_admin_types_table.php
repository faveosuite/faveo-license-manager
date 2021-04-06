<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflAdminTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_admin_types', function (Blueprint $table) {
            $table->primary('admin_type_id');
            $table->increments('admin_type_id')->unique();
            $table->string('admin_type_name',125);
            $table->string('admin_type_title',125);
            $table->boolean('admin_type_status');
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
        Schema::dropIfExists('afl_admin_types');
    }
}
