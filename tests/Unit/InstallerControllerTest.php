<?php

namespace Tests\Unit;

use Tests\TestCase;

class InstallerControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_createenv_updateversion()
    {
        $this->post(route('create.env'));
        $response = $this->call('POST', url('create/env'), [
            $api = true,
            $appUrl = 'https://qa.faveodemo.com/sowmya/license/public/',
            $host = 'localhost',
            $database = 'qafaveo_license',
            $dbusername = 'rafaveo_sowmya',
            $dbpassword = 'sowmyasowmya',
            $default = null,
            $port = 3306,
        ]);
        $response->assertStatus(200);
    }

     public function test_accountcheck_updatedetails()
     {
         $this->withoutMiddleware();
         $this->post(route('final'));
         $response = $this->call('POST', url('final'), [
             'admin_fname' => 'sowmi',
             'admin_lname' => 's',
             'admin_email' => 'sowmi@gmail.com',
             'admin_password' => 'Sowmi@123',

         ]);
         $response->assertStatus(200);
     }

     public function test_migrate_updateversion()
     {
         $this->withoutMiddleware();
         $this->post(route('migrate'));
         $response = $this->call('POST', url('migrate'));
         $response->assertStatus(200);
     }

     public function test_checkPreInstall()
     {
         $this->post(route('preinstall.check'));
         $response = $this->call('POST', url('preinstall/check'));
         $response->assertStatus(200);
     }
}
