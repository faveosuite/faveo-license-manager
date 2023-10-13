<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflClients;
use Tests\TestCase;

class ClientsControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_clientAdd_whenClientDetailsIsAdded_shouldRecieveResponse201()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',

            'client_fname' => 'Sandesh',
            'client_lname' => 'Menath',
            'client_email' => 'sandesh@gmail.com',
            'client_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/clients/add'), $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Client has been Added Successfully']);
        $response->assertJson(['data' => 1]);
    }

    public function test_clientAdd_whenClientDetailsIsAddedWithSameDetails_shouldRecieveResponse412()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',

            'client_fname' => 'Sandesh',
            'client_lname' => 'Menath',
            'client_email' => 'sandesh@gmail.com',
            'client_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/clients/add'), $data);
        $response->assertStatus(412);
    }

    public function test_clientUpdate_whenClientDetailsIsUpdated_shouldRecieveResponse200()
    {
        $this->withoutMiddleware();
        $id = AflClients::where('client_email', 'sandesh@gmail.com')->value('client_id');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',

            'client_id' => $id,
            'client_fname' => 'Sandesh',
            'client_lname' => 'Men',
            'client_email' => 'sandesh123@gmail.com',
            'client_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/clients/edit'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Client Deatils has been updated Successfully']);
        $response->assertJson(['data' => 1]);
    }

    public function test_deleteClient_whenClientDetailsIsDeleted_shouldRecieveResponse200()
    {
        $this->withoutMiddleware();
        $id = AflClients::where('client_email', 'sandesh123@gmail.com')->value('client_id');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',

            'client_id' => $id,
        ];
        $response = $this->json('POST', url('api/admin/clients/delete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Client has been deleted Successfully']);
    }

    public function test_clientAdd_whenClientDetailsIsAddedWithInvalidDetails_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',

            'client_fname' => 'Sandesh',
            'client_lname' => 'Menath',
            'client_email' => 'sandesh@gmail.com',
        ];
        $response = $this->json('POST', url('api/admin/clients/add'), $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }
}
