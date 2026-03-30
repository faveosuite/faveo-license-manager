<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflClients;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\AflApiKeys;


class ClientsControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_clientAdd_whenClientDetailsIsAdded_shouldRecieveResponse201()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => AflApiKeys::first()->api_key_secret,

            'client_fname' => 'Sandesh',
            'client_lname' => 'Menath',
            'client_email' => 'sandesh@gmail.com',
            'client_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/clients/add'), $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Contact has been Added Successfully']);
        $this->assertNotEmpty(json_decode($response->getContent())->data->client_id);
    }

    public function test_clientAdd_whenClientDetailsIsAddedWithSameDetails_shouldRecieveResponse412()
    {
        $this->withoutMiddleware();
        AflClients::create([
            'client_fname' => 'Sandesh',
            'client_lname' => 'Menath',
            'client_email' => 'sandesh@gmail.com',
            'client_status' => 1,
        ]);
        $data = [
            'api_key_secret' => AflApiKeys::first()->api_key_secret,

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
        $id = AflClients::create([
            'client_fname' => 'Sandesh',
            'client_lname' => 'Menath',
            'client_email' => 'sandesh@gmail.com',
            'client_status' => 1,
        ])->value('client_id');
        $data = [
            'api_key_secret' => AflApiKeys::first()->api_key_secret,

            'client_id' => $id,
            'client_fname' => 'Sandesh',
            'client_lname' => 'Men',
            'client_email' => 'sandesh123@gmail.com',
            'client_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/clients/edit'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Contact Details has been updated Successfully']);
        $response->assertJson(['data' => 1]);
    }

    public function test_deleteClient_whenClientDetailsIsDeleted_shouldRecieveResponse200()
    {
        $this->withoutMiddleware();
        $id = AflClients::where('client_email', 'sandesh123@gmail.com')->value('client_id');
        $data = [
            'api_key_secret' => AflApiKeys::first()->api_key_secret,

            'client_id' => $id,
        ];
        $response = $this->json('POST', url('api/admin/clients/delete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Contact has been deleted Successfully']);
    }

    public function test_clientAdd_whenClientDetailsIsAddedWithInvalidDetails_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => AflApiKeys::first()->api_key_secret,

            'client_fname' => 'Sandesh',
            'client_lname' => '',
            'client_email' => 'sandesh@gmail.com',
        ];
        $response = $this->json('POST', url('api/admin/clients/add'), $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }
}
