<?php

namespace Tests\Unit;

use App\Models\AflClients;
use Tests\TestCase;

class EditProfilesControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_editProfile_editDetailsOfUserNotPresent_shouldRespondWith404()
     
    {
        $this->withoutMiddleware();
        $admin = AflClients::factory()->create(['client_id' => rand(1000,9999)]);
        $admin_id = $admin->client_id;
        $data = [
            'admin_fname' => 'John',
            'admin_lname' => 'Doe',
            'admin_email' => 'johndoe@example.com',
            'admin_password' => 'secretpassword',
            'admin_email_confirmation' => 'johndoe@example.com',
             'admin_password_confirmation' => 'secretpassword',
        ];

        $response = $this->post("api/admin/editprofile/{$admin_id}", $data);
        $response->assertStatus(200);
        $this->assertEquals('John', AflClients::find($admin_id)->client_fname);
        $this->assertEquals('Doe', AflClients::find($admin_id)->client_lname);
        $this->assertEquals('johndoe@example.com', AflClients::find($admin_id)->client_email);
        $admin->delete();

    }
}
