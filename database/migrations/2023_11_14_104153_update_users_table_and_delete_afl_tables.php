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
        Schema::table('users', function (Blueprint $table) {
            $table->increments('client_id')->unique();
            $table->string('client_fname', 125);
            $table->string('client_lname', 125);
            $table->string('client_email', 125)->unique();
            $table->string('client_password', 125)->nullable();
            $table->string('client_role', 125)->default('client');
            $table->date('client_active_date')->nullable();
            $table->date('client_cancel_date')->nullable();
            $table->boolean('client_status')->default('1');
            $table->dropColumn(['id','name','email','email_verified_at','password','remember_token']); 
            $table->dropColumn('client_fname'); 
            $table->dropColumn('client_lname'); 
            $table->dropColumn('client_email'); 
            $table->dropColumn('client_password'); 
            $table->dropColumn('client_active_date'); 
            $table->dropColumn('client_cancel_date'); 
            $table->dropColumn('client_status'); 
            $table->timestamps();
        });

       // Schema::dropIfExists('afl_clients');
        //Schema::dropIfExists('afl_admins');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->dropColumn(['client_id','client_fname','client_lname','client_email','client_password','client_active_date','client_cancel_date','client_cancel_date','client_status']); 
        });

       // Schema::create('afl_clients', function (Blueprint $table) {
            //$table->increments('client_id')->unique();
            //$table->string('client_fname', 125);
           // $table->string('client_lname', 125);
           // $table->string('client_email', 125);
            //$table->date('client_active_date')->nullable();
            //$table->date('client_cancel_date')->nullable();
            //$table->boolean('client_status')->default('1');
            //$table->timestamps();
        //});

    // Schema::create('afl_admins', function (Blueprint $table) {
       //     $table->increments('admin_id')->unique();
         //   $table->string('admin_fname', 125);
           // $table->string('admin_lname', 125);
            //$table->string('admin_email', 125)->unique();
         //   $table->string('admin_password', 125);
           // $table->string('admin_reset', 125)->default('null');
         //   $table->boolean('admin_data_authenticity')->default(1);
           // $table->string('admin_ip', 125)->default('null');
           //  $table->integer('admin_type_id')->nullable()
                // ->constrained('afl_admin_types', 'admin_type_id')
              //   ->onDelete('cascade');
           //  $table->date('admin_date');
           //  $table->string('admin_hash', 125)->default('null');    
           //  $table->boolean('admin_status')->default(1);
           //  $table->timestamps();
       //  });
    }
};
