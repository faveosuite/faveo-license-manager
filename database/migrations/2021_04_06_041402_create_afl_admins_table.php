<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAflAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_admins', function (Blueprint $table) {
            //$table->primary('admin_id');
            $table->increments('admin_id')->unique();
            $table->string('admin_fname', 125);
            $table->string('admin_lname', 125);
            $table->string('admin_email', 125)->unique();
            $table->string('admin_password', 125);
            $table->string('admin_reset', 125)->default('null');
            //$table->string('admin_reset',125);
            $table->boolean('admin_data_authenticity')->default(1);
            $table->string('admin_ip', 125)->default('null');

            $table->integer('admin_type_id')->nullable()
                ->constrained('afl_admin_types', 'admin_type_id')
                ->onDelete('cascade');

            // $table->integer('admin_type_id')->unsigned()->nullable();
            //$table->foreign('admin_type_id')->references('admin_type_id')->on('afl_admin_types')->onDelete('cascade');

            // $table->integer('admin_type_id')->unsigned()->nullable();
            //$table->foreign('admin_type_id')->references('admin_type_id')->on('afl_admin_types')->onDelete('cascade');

            $table->date('admin_date');
            $table->string('admin_hash', 125)->default('null');    //->unique()
            $table->boolean('admin_status')->default(1);

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
        Schema::dropIfExists('afl_admins');
    }
}
