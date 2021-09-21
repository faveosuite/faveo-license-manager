<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflSettings;
use Tests\TestCase;
use App\Models\AflApiKeys;

class ApiKeyControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_apiKeyAdd_whenApiKeyIsAdded_shouldReturn200()
    {
         AflSettings::factory()->create();
         $data=['token' => env('LICENSE_KEY'),
         'api_key_secret' => 'P5Zp2PmbOSPWOdc6',
         'api_key_ip' =>'',
         'api_key_clients_add' => 1,
         'api_key_clients_edit' => 1,
         'api_key_licenses_add' => 1,
         'api_key_licenses_edit' => 1,
         'api_key_products_add' => 1,
         'api_key_products_edit' => 1,
         'api_key_installations_edit'=>1,
         'api_key_search' =>1,
         'api_key_status'=>1];
         $response = $this->json('POST',url('api/admin/addnewapi'),$data);
         $response->assertStatus(201);
         $response->assertJson(['success'=>true]);
         $response->assertJson(['message'=>'lang.add']);
    }

    public function test_apiKeyUpdate_whenApiKEyIsUpdated_shouldReturnTrue()
    {

       $id=AflApiKeys::where('api_key_secret','P5Zp2PmbOSPWOdc6')->value('api_key_id');

         $data=['token' => env('LICENSE_KEY'),
         'api_key_secret' => 'P5Zp2PmbOSPWOdc6666',
         'api_key_ip' =>'',
         'api_key_clients_add' => 1,
         'api_key_clients_edit' => 1,
         'api_key_licenses_add' => 1,
         'api_key_licenses_edit' => 1,
         'api_key_products_add' => 1,
         'api_key_products_edit' => 1,
         'api_key_installations_edit'=>1,
         'api_key_search' =>1,
         'api_key_status'=>1];
         $response = $this->json('POST',url('api/admin/editnewapi/'.$id),$data);
         $response->assertStatus(200);
         $response->assertJson(['success' => true]);
         $response->assertJson(['message' => "lang.Update"]);
         $response->assertJson(['data' => 1]);

    }
    public function test_apiKeyDelete_whenApiKEyIsDeleted_shouldReturnTrue()
    {

       $id=AflApiKeys::where('api_key_secret','P5Zp2PmbOSPWOdc6666')->value('api_key_id');
        $data=['token' => env('LICENSE_KEY')];
         $response = $this->json('DELETE',url('api/admin/deleteapi/'.$id),$data);
         $response->assertStatus(200);
         $response->assertJson(['success' => true]);
         $response->assertJson(['message' => "lang.Delete"]);
         $response->assertJson(['data' => 1]);

    }
    public function test_apiKeyAdd_whenApiKeyIsAddedPermanently_shouldReturn200()
    {

         $api=AflApiKeys::where('api_key_secret','5hDuaXuTh9gTLfPL')->get()->toArray();
         if(empty($api)){
         $data=['token' => env('LICENSE_KEY'),
         'api_key_secret' => '5hDuaXuTh9gTLfPL',
         'api_key_ip' =>'',
         'api_key_clients_add' => 1,
         'api_key_clients_edit' => 1,
         'api_key_licenses_add' => 1,
         'api_key_licenses_edit' => 1,
         'api_key_products_add' => 1,
         'api_key_products_edit' => 1,
         'api_key_installations_edit'=>1,
         'api_key_search' =>1,
         'api_key_status'=>1];
         $response = $this->json('POST',url('api/admin/addnewapi'),$data);
         $response->assertStatus(201);
         $response->assertJson(['success'=>true]);
         $response->assertJson(['message' => 'lang.add']);
     }
     else{
         $this->assertTrue(true);
     }
    }


}
