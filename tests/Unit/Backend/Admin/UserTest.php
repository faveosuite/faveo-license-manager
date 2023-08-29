<?php

namespace Tests\Unit\Backend\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AflAdmins;
use Tests\TestCase;
use Illuminate\Support\Facades\Lang;




class UserTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function GetUserTest()
    {
        $this->withoutMiddleware();
        $user_name = AflAdmins::factory()->create()->user_name;
        $first_name = AflAdmins::factory()->create()->first_name;
        $email = AflAdmins::factory()->create()->email;

        $result = $this->assertDatabaseHas('user_name', ['user_name' => $user_name]);
        $this->assertDatabaseHas('first_name', ['first_name' => $first_name]);
        $this->assertDatabaseHas('email', ['email' => $email]);
        $response = $this->call('GET', url("api/admin/getusers"));
        $response->assertStatus(200);
        $this->assertEquals(1, json_decode($response->getContent())->data->user_name);
    }

    public function AddUserTest()
    {
        $this->withoutMiddleware();
        $data = [
            'admin_fname' => 'Gurmeen',
            'admin_lname ' => 'Kour',
            'admin_email' => 'gurmeen@gmail.com',
            'admin_date' => '28-08-2022',
            'admin_status' => '1',
        ];
        $response = $this->json('POST', url('api/admin/addusers'), $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Users details has been added']);
        $response->assertJson(['data' => 1]);
    }
    public function EditUserTest()
    {
        $this->withoutMiddleware();
        $response = $this->json('GET', url('api/admin/editusers/1'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function UpdateUserTest()
    {
        $this->withoutMiddleware();
        $data = [
            'admin_fname' => 'Gurmeen',
            'admin_lname ' => 'Kour',
            'admin_email' => 'gurmeen@gmail.com',
            'admin_date' => '28-08-2022',
            'admin_status' => '1',
        ];
        $response = $this->json('POST', url('api/admin/addusers'), $data);
        $response->assertStatus(405);
    }
  
    public function DeleteUserTest()
    {
        $this->withoutMiddleware();
        $id = AflAdmins::factory()->create()->id;
        $data = [
            'admin_id' => $id,
        ];
        $response = $this->json('POST', url('api/admin/deleteusers/$id'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => Lang::get('lang.user_deleted')]);
    }
 

}
