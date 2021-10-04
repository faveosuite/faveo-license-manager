<?php

namespace Tests\Unit\Backend\Api;

use App\Models\AflAdmins;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_register_whenAdminRegisters_shouldReturnResponse201()
    {
      $data = [
          'admin_fname'=>'Sandesh',
          'admin_lname'=>'Menath',
          'admin_email'=>'sandesh@123gamil.com',
          'admin_password'=>'sandesh123',
          'admin_password_confirmation' =>'sandesh123'
      ];
      $response = $this->json('POST',url('api/register'),$data);
      $response->assertStatus(201);
      $response->assertJson(['success'=>true]);
      $response->assertJson(['message'=>'You Have registered successfuly to Auto Faveo Licenser']);
    }
    public function test_login_whenAdminLogsIn_shouldReturnResponse200()
    {
        $data = [
            'admin_email'=>'sandesh@123gamil.com',
            'admin_password'=>'sandesh123',
        ];
        $response = $this->json('POST',url('api/login'),$data);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message'=>'You have logged in successfully to Auto Faveo Licenser']);
    }
    public function test_logout_whenAdminLogsOut_shouldReturnResponse200()
    {
        $this->withoutMiddleware();
        $id = AflAdmins::where('admin_email','sandesh@123gamil.com')->value('admin_id');
        $data = [
            'token' =>env('LICENSE_KEY'),
        ];
        $response = $this->json('POST',url('api/admin/logout/'.$id),$data);
        $response->assertStatus(201);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message'=>'You have has logged out successfully from Auto Faveo Licenser']);
    }
    public function test_forgot_whenAdminForgetsPassword_shouldReturnResponse200()
    {
        $data = [
            'admin_email'=>'sandesh@123gamil.com',
        ];
        $response = $this->json('POST',url('api/forgot'),$data);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message'=>'We have emailed your password reset link!']);
    }

    public function test_reset_whenAdminResetsthePassword_shouldReturnResponse200(){
        $token = DB::table('password_resets')
                    ->where('email','sandesh@123gamil.com')
                     ->value('token');
        $data = [
            'email' => 'sandesh123@gamil.com',
            'token' =>$token,
                  'password' => 'sandesh1234',
                  'password_confirmation' =>'sandesh1234'
                ];
        $response = $this->json('POST',url('api/reset'),$data);
        $response->assertStatus(201);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message'=>'Your password has been reset!']);
        $response->assertJson(['data'=>1]);
    }

    public function test_login_whenAdminLogsInWithNewCredentials_shouldReturnResponse200()
    {
        $data = [
            'admin_email'=>'sandesh@123gamil.com',
            'admin_password'=>'sandesh1234',
        ];
        $response = $this->json('POST',url('api/login'),$data);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message'=>'You have logged in successfully to Auto Faveo Licenser']);

    }
    public function test_reset_whenAdminResetsthePasswordWithInvalidToken_shouldReturnResponse401(){
        $data = [
            'email' => 'sandesh123@gamil.com',
            'token' =>'sjsjsjsjsjjs',
            'password' => 'sandesh1234',
            'password_confirmation' =>'sandesh1234'
        ];
        $response = $this->json('POST',url('api/reset'),$data);
        $response->assertStatus(401);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message'=>'This password reset token is invalid.']);
    }
    public function test_logout_whenAdminLogsOutAfterPAsswordChange_shouldReturnResponse200()
    {
        $this->withoutMiddleware();
        $id = AflAdmins::where('admin_email','sandesh@123gamil.com')->value('admin_id');
        $response = $this->json('POST',url('api/admin/logout/'.$id));
        $response->assertStatus(201);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message'=>'You have has logged out successfully from Auto Faveo Licenser']);
        AflAdmins::where('admin_email','sandesh@123gamil.com')->delete();
    }
    public function test_login_whenAdminLogsInWithWrongCredentials_shouldReturnResponse401()
    {
        $data = [
            'admin_email'=>'sandesh@sdkdfdk123gamil.com',
            'admin_password'=>'sandesh123djsdnff',
        ];
        $response = $this->json('POST',url('api/login'),$data);
        $response->assertStatus(401);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message'=>'These credentials do not match our records.']);
    }
    public function test_forgot_whenAdminForgetsPasswordAndWrongEmail_shouldReturnResponse404()
    {
        $data = [
            'admin_email'=>'sandedddddddddddsh@123gamil.com',
        ];
        $response = $this->json('POST',url('api/forgot'),$data);
        $response->assertStatus(404);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message'=>'These credentials do not match our records.']);
    }
    public function test_reset_whenAdminResetsthePasswordWithWrongValidation_shouldReturnResponse401(){
        $token = DB::table('password_resets')
            ->where('email','sandesh@123gamil.com')
            ->value('token');
        $data = [
            'email' => 'sandesh123',
            'token' =>$token,
            'password' => 'sandesh1234',
            'password_confirmation' =>'sandesh1234'
        ];
        $response = $this->json('POST',url('api/reset'),$data);
        $response->assertStatus(401);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message'=>'The details entered into the form seems to be incomplete']);

    }

}
