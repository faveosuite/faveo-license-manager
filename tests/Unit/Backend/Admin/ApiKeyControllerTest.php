<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflApiKeys;
use App\Models\AflSettings;
use Tests\TestCase;

class ApiKeyControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    // public function test_apiKeyAdd_whenApiKeyIsAdded_shouldReturn200()
    // {
    //     $this->withoutMiddleware();
    //     AflSettings::factory()->create();
    //     AflApiKeys::where('api_key_secret', '5hDuaXuTh9gTLfPL')->delete();
    //     $data = [
    //         'api_key_secret' => 'P5Zp2PmbOSPWOdc6',
    //         'api_key_ip' => '',
    //         'api_key_clients_add' => 1,
    //         'api_key_clients_edit' => 1,
    //         'api_key_licenses_add' => 1,
    //         'api_key_licenses_edit' => 1,
    //         'api_key_products_add' => 1,
    //         'api_key_products_edit' => 1,
    //         'api_key_installations_edit' => 1,
    //         'api_key_search' => 1,
    //         'api_key_status' => 1, ];
    //     $response = $this->json('POST', url('api/admin/addnewapi'), $data);
    //     $response->assertStatus(201);
    //     $response->assertJson(['success' => true]);
    //     $response->assertJson(['message' => 'API Key Added Successfully']);
    // }

    // public function test_apiKeyUpdate_whenApiKeyIsUpdated_shouldReturnTrue()
    // {
    //     $this->withoutMiddleware();
    //     $id = AflApiKeys::where('api_key_secret', 'P5Zp2PmbOSPWOdc6')->value('api_key_id');

    //     $data = [
    //         'api_key_secret' => 'P5Zp2PmbOSPWOdc6666',
    //         'api_key_ip' => '',
    //         'api_key_clients_add' => 1,
    //         'api_key_clients_edit' => 1,
    //         'api_key_licenses_add' => 1,
    //         'api_key_licenses_edit' => 1,
    //         'api_key_products_add' => 1,
    //         'api_key_products_edit' => 1,
    //         'api_key_installations_edit' => 1,
    //         'api_key_search' => 1,
    //         'api_key_status' => 1, ];
    //     $response = $this->json('POST', url('api/admin/editnewapi/'.$id), $data);
    //     $response->assertStatus(200);
    //     $response->assertJson(['success' => true]);
    //     $response->assertJson(['message' => 'The Current API Key Details Has Been Updated']);
    //     $response->assertJson(['data' => 1]);
    // }

    // public function test_apiKeyDelete_whenApiKeyIsDeleted_shouldReturnTrue()
    // {
    //     $this->withoutMiddleware();
    //     $id = AflApiKeys::where('api_key_secret', 'P5Zp2PmbOSPWOdc6666')->value('api_key_id');
    //     $data = ['token' => env('LICENSE_KEY')];
    //     $response = $this->json('POST', url('api/admin/deleteapi/'.$id), $data);
    //     $response->assertStatus(200);
    //     $response->assertJson(['success' => true]);
    //     $response->assertJson(['message' => 'API Key Deleted Successfully']);
    //     $response->assertJson(['data' => 1]);
    // }

    // public function test_apiKeyAdd_whenApiKeyIsAddedPermanently_shouldReturn200()
    // {
    //     $this->withoutMiddleware();
    //     $api = AflApiKeys::where('api_key_secret', '5hDuaXuTh9gTLfPL')->get()->toArray();
    //     if (empty($api)) {
    //         $data = [
    //             'api_key_secret' => '5hDuaXuTh9gTLfPL',
    //             'api_key_ip' => '',
    //             'api_key_clients_add' => 1,
    //             'api_key_clients_edit' => 1,
    //             'api_key_licenses_add' => 1,
    //             'api_key_licenses_edit' => 1,
    //             'api_key_products_add' => 1,
    //             'api_key_products_edit' => 1,
    //             'api_key_installations_edit' => 1,
    //             'api_key_search' => 1,
    //             'api_key_status' => 1, ];
    //         $response = $this->json('POST', url('api/admin/addnewapi'), $data);
    //         $response->assertStatus(201);
    //         $response->assertJson(['success' => true]);
    //         $response->assertJson(['message' => 'API Key Added Successfully']);
    //     } else {
    //         $this->assertTrue(true);
    //     }
    // }
}
