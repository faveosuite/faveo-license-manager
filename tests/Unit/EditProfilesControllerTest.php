<?php

namespace Tests\Unit;

use App\Models\AflAdmins;
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
        AflAdmins::factory()->create(['admin_id' => rand(1000,9999)]);
        $data = [
            'admin_fname' => 'Sandesssssh',
            'admin_lname' => 'Menaaaaaaaaaath',
            'admin_email' => 'sowmi@gmail.com',
            'admin_email_confirmation' => 'sowmi@gmail.com',
            'admin_password' => 'sandesh123',
            'admin_password_confirmation' => 'sandesh123',
            'admin_data_authenticity' => 1,
        ];
        $response = $this->json('POST', url('api/admin/editprofile/2'), $data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.error']);
}

    public function test_editProfile_editDetailsOfTheLoggedInUser_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [
            'admin_fname' => 'Sandesssssh',
            'admin_lname' => 'Menaaaaaaaaaath',
            'admin_email' => 'sowmi@gmail.com',
            'admin_email_confirmation' => 'sowmi@gmail.com',
            'admin_password' => 'sandesh123',
            'admin_password_confirmation' => 'sandesh123',
            'admin_data_authenticity' => 1,
        ];
        $response = $this->json('POST', url('api/admin/editprofile/3'), $data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.error']);
        AflAdmins::where('admin_id', 2)->delete();
    }
}
