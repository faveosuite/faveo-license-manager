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
        $response = $this->call('POST', url('api/admin/storelogosettings'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('logos', ['logo_title' => 'logo','logo_type' => 'sidebar']);
    }
    public function test_getdata_from_expire_updates_displaytable()
    {
        $this->withoutMiddleware();
        $title = Logos::factory()->create()->logo_title;
        $this->assertDatabaseHas('logos', ['logo_title' => $title]);
        $response = $this->call('GET', url("api/admin/getlogos"));
        $response->assertStatus(200);
        $this->assertEquals('My Logo Title', json_decode($response->getContent())->login_logo[0]->logo_title);
    }
}