<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflBannedHosts;
use Tests\TestCase;

class BannedHostControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_bannedHostAdd_whenBannedHostIsAdded_shouldRecieveResponse201()
    {
        $data=[
            'api_key_secret' =>'5hDuaXuTh9gTLfPL',
            'token' => env('LICENSE_KEY'),
            'banned_host_ip' => '127.0.0.1',
            'banned_host_comments' =>'Testing by banning a host',
            'banned_host_blocks' => 2,
            'banned_host_last_block_date' => '2020-09-12'
        ];
        $response = $this->json('POST',url('api/admin/bannedHosts/add'),$data);
        $response->assertStatus(201);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message' => 'A new Banned Host has been added from Auto Faveo Licenser']);

    }
    public function test_bannedHostUpdate_whenBannedHostIsUpdatedWithoutId_shouldRecieveResponse400()
    {
        $data=[
            'api_key_secret' =>'5hDuaXuTh9gTLfPL',
            'token' => env('LICENSE_KEY'),
            'banned_host_ip' => '127.0.0.2',
            'banned_host_comments' =>'Testing by banning a host',
        ];
        $response = $this->json('POST',url('api/admin/bannedHosts/edit'),$data);
        $response->assertStatus(400);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }
    public function test_bannedHostUpdate_whenBannedHostIsUpdatedWithoutIp_shouldRecieveResponse400()
    {

        $data=[
            'api_key_secret' =>'5hDuaXuTh9gTLfPL',
            'token' => env('LICENSE_KEY'),
            'banned_host_id'=>1,
            'banned_host_comments' =>'Testing by banning a host',
        ];
        $response = $this->json('POST',url('api/admin/bannedHosts/edit'),$data);
        $response->assertStatus(400);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }
    public function test_bannedHostUpdate_whenBannedHostIsUpdated_shouldRecieveResponse201()
    {
        $banned_id=AflBannedHosts::where('banned_host_ip','127.0.0.1')->value('banned_host_id');
        $data=[
            'api_key_secret' =>'5hDuaXuTh9gTLfPL',
            'token' => env('LICENSE_KEY'),
            'banned_host_id'=>$banned_id,
            'banned_host_ip' => '127.0.0.2',
            'banned_host_comments' =>'Testing by banning a host',
        ];
        $response = $this->json('POST',url('api/admin/bannedHosts/edit'),$data);
        $response->assertStatus(201);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message' => 'Banned Host details of Auto Faveo Licenser has been updated']);
        $response->assertJson(['data'=>1]);
    }
    public function test_deleteBannedHost_whenBannedHostIsDeleted_shouldRecieveResponse201()
    {
        $banned_id=AflBannedHosts::where('banned_host_ip','127.0.0.2')->value('banned_host_id');
        $data=[
            'api_key_secret' =>'5hDuaXuTh9gTLfPL',
            'token' => env('LICENSE_KEY'),
            'banned_host_id'=>$banned_id,
        ];
        $response = $this->json('POST',url('api/admin/bannedHosts/delete'),$data);
        $response->assertStatus(201);
        $response->assertJson(['success'=>true]);
        $response->assertJson(['message' => 'The record you have selected has been deleted from the Auto Faveo License Manager Database']);
        $response->assertJson(['data'=>1]);
    }
    public function test_bannedHostAdd_whenBannedHostIsAddedWithInvalidIp_shouldRecieveResponse400()
    {
        $data=[
            'api_key_secret' =>'5hDuaXuTh9gTLfPL',
            'token' => env('LICENSE_KEY'),
            'banned_host_comments' =>'Testing by banning a host',
            'banned_host_blocks' => 2,
            'banned_host_last_block_date' => '2020-09-12'
        ];
        $response = $this->json('POST',url('api/admin/bannedHosts/add'),$data);
        $response->assertStatus(400);
        $response->assertJson(['success'=>false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);

    }

}
