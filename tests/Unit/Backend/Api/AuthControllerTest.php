<?php

use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\AflClients;
use Illuminate\Support\Facades\DB;

class AuthControllerTest extends TestCase
{
    protected $testUser;

    /**
     *
     * @return AflClients
     */
    protected function getTestUser()
    {
        if (!$this->testUser) {
            $faker = Faker::create();

            $this->testUser = AflClients::factory()->create([
                'client_email' => $faker->safeEmail,
                'client_password' => Hash::make('Password@1'), 
                'client_role' => 'admin', 
            ]);
        }

        return $this->testUser;
    }

    /**
     * Test user login with valid credentials.
     *
     * @return void
     */
    public function testUserLoginWithValidCredentials()
    {
        $user = $this->getTestUser(); 

        $response = $this->post('/api/login', [
            'client_email' => $user->client_email,
            'client_password' => 'Password@1',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => Lang::get('lang.Login'),
            ]);
    }

    /**
     * Test user login with invalid credentials.
     *
     * @return void
     */
    public function testUserLoginWithInvalidCredentials()
    {
        $response = $this->post('/api/login', [
            'client_email' => 'invalidemail@gmail.com', 
            'client_password' => 'invalidpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => Lang::get('auth.failed'),
            ]);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testUserLogout()
    {
        $user = $this->getTestUser(); 

        $token = $user->createToken('AFL')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/admin/logout/' . $user->client_id);

        $response->assertStatus(201)
            ->assertJson([
                'message' => Lang::get('lang.Logout'),
            ]);
    }

    /**
     * Test forgot password functionality.
     *
     * @return void
     */
    public function testForgotPassword()
    {
        $user = $this->getTestUser(); 

        $response = $this->post('/api/forgot', [
            'admin_email' => $user->client_email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'We have emailed your password reset link!',
            ]);
    }

    /**
     * Test password reset functionality.
     *
     * @return void
     */
    public function testPasswordReset()
    {
        $user = $this->getTestUser(); 

        $resetToken = Str::random(10);

        DB::table('password_resets')->insert([
            'email' => $user->client_email,
            'token' => $resetToken,
        ]);

        $newPassword = 'newpassword123';

        $response = $this->post('/api/reset', [
            'email' => $user->client_email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $resetToken,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => Lang::get('passwords.reset'),
            ]);
    }

}






