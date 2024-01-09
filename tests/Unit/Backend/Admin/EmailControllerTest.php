<?php

namespace Tests\Unit\Backend\Admin;

use Tests\TestCase;
use App\Models\AflSettings;

class EmailControllerTest extends TestCase
{

 public function testgetSettingsEmail()
    {
        $this->withoutMiddleware();
        $aflLicensesId = AflSettings::factory()->create(
            [
                'EMAIL_DRIVER' => 'SMTP',
                'EMAIL_PASSWORD' => 'password',
                'EMAIL_ENCRYPTION' => 'ssl',
                'EMAIL_HOST' => 'smtp.gmail.com',
                'EMAIL_SENDING_STATUS' => "1",
            ]);
        $result = $this->assertDatabaseHas('afl_settings', ['EMAIL_DRIVER' => 'SMTP']);

        $response = $this->call('GET', url("api/admin/emailSettings"));
        $response->assertStatus(200);
       
    }
    public function testgetSettingfornopasswordforSMTP()
    {
        $this->withoutMiddleware();
        $data = [
            'EMAIL_DRIVER' => 'SMTP',
            'EMAIL_ENCRYPTION' => 'SSL',
            'EMAIL_HOST' => 'smtp.gmail.com',
            'EMAIL_SENDING_STATUS' => "1",
        ];
        $response = $this->call('POST', url("api/admin/emailSettings"));
        $response->assertStatus(412);
       
    }
    public function testgetSettingforwrongdata()
    {
        $this->withoutMiddleware();
        $data = [
            'EMAIL_DRIVER' => 'SMTP',
            'EMAIL_PASSWORD' => 'password',
            'EMAIL_ENCRYPTION' => 'SSL',
            'EMAIL_HOST' => 'smtp.com',
            'EMAIL_SENDING_STATUS' => "1",
        ];
        $response = $this->call('POST', url("api/admin/emailSettings"));
        $response->assertStatus(412);
       
    }
    public function testgetSettingforphpmailwitNoFormAdress()
    {
        $this->withoutMiddleware();
        $data = [
            'EMAIL_DRIVER' => 'mail',
        ];
        $response = $this->call('POST', url("api/admin/emailSettings"));
        $response->assertStatus(412);
       
    }
    public function testgetSettingforphpmailwitNoDriver()
    {
        $this->withoutMiddleware();
        $data = [
            'EMAIL_FROM_ADDRESS' => 'gurmeen.kour@mail.com',
        ];
        $response = $this->call('POST', url("api/admin/emailSettings"));
        $response->assertStatus(412);
       
    }
    public function testgetSettingwithnoEncryption()
    {
        $this->withoutMiddleware();
        $data = [
            'EMAIL_DRIVER' => 'SMTP',
            'EMAIL_PASSWORD' => 'password',
            'EMAIL_HOST' => 'smtp.com',
            'EMAIL_SENDING_STATUS' => "1",
        ];
        $response = $this->call('POST', url("api/admin/emailSettings"));
        $response->assertStatus(412);
       
    }
    public function testgetSettingsEmaiphpl()
    {
        $this->withoutMiddleware();
        $aflLicensesId = AflSettings::factory()->create(
            [
                'EMAIL_DRIVER' => 'PHP',
                'EMAIL_FROM_ADDRESS' => "gurmeen.kour@mail.com",
            ]);
        $result = $this->assertDatabaseHas('afl_settings', ['EMAIL_DRIVER' => 'SMTP']);

        $response = $this->call('GET', url("api/admin/emailSettings"));
        $response->assertStatus(200);
       
    }
    public function testgetSettingwithnoEncryptionandhost()
    {
        $this->withoutMiddleware();
        $data = [
            'EMAIL_DRIVER' => 'SMTP',
            'EMAIL_PASSWORD' => 'password',
            'EMAIL_SENDING_STATUS' => "1",
        ];
        $response = $this->call('POST', url("api/admin/emailSettings"));
        $response->assertStatus(412);
       
    }

    
  
}
