<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\Logos;
use Illuminate\Http\UploadedFile;

use Tests\TestCase;


use function GuzzleHttp\json_decode;

class SettingsControllerTest extends TestCase
{
    
    public function test_logos()
    {
        $this->withoutMiddleware();
        $data = [
            'logo_title' => 'My Logo Title',
            'login_image' => UploadedFile::fake()->image('login_image.jpg'),
        ];
        $response = $this->call('POST', url('api/admin/store-logo-settings'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('logos', ['logo_title' => 'My Logo Title','logo_type' => 'login']);
    }
    // 
    public function testGetLogos()
    {
        // Mock the logos model
        $this->withoutMiddleware();

        $data = [
            'logo_title' => str()->random(),
            'login_image' => UploadedFile::fake()->image('login_image.jpg'),
        ];
        $response = $this->call('POST', url('api/admin/store-logo-settings'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('logos', ['logo_title' => $data['logo_title']]);
        $response = $this->get('/api/admin/getLogos');
        $response->assertStatus(200);
        $responseData = json_decode($response->getContent())->login_logo;
        $logoAddedd = $responseData[count($responseData) - 1];
        $this->assertEquals($logoAddedd->logo_title, $data['logo_title']);
    }
}