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
     Schema::dropIfExists('users');
     Schema::table('afl_clients', function (Blueprint $table) {
       $table->string('client_password', 255)->nullable()->after('client_email');
      $table->string('client_role', 20)->default('client')->after('client_status');
      $table->string('client_username', 20)->nullable()->after('client_lname');
      });
      Schema::rename('afl_clients', 'users');
      //Schema::dropIfExists('afl_admins');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
      Schema::rename('users', 'afl_clients');
      Schema::table('afl_clients', function (Blueprint $table) {
        $table->dropColumn('client_password');
        $table->dropColumn('client_role');
    });
    
   Schema::create('users', function (Blueprint $table) {
        $table->id();
       $table->string('name');
       $table->string('email')->unique();
       $table->timestamp('email_verified_at')->nullable();
       $table->string('password');
       $table->rememberToken();
       $table->timestamps();
        });
     //Schema::create('afl_admins', function (Blueprint $table) {
          // $table->increments('admin_id')->unique();
           //$table->string('admin_fname', 125);
           //$table->string('admin_lname', 125);
           //$table->string('admin_email', 125)->unique();
           //$table->string('admin_password', 125);
           //$table->string('admin_reset', 125)->default('null');
           //$table->boolean('admin_data_authenticity')->default(1);
           //$table->string('admin_ip', 125)->default('null');
           //$table->integer('admin_type_id')->nullable()
           //->constrained('afl_admin_types', 'admin_type_id')
              // ->onDelete('cascade');
           //$table->date('admin_date');
           //$table->string('admin_hash', 125)->default('null');    
           //$table->boolean('admin_status')->default(1);
           //$table->timestamps();
       //});
    }
};
