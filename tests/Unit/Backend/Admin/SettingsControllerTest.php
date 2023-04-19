<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\ExpireUpdatesDisplay;
use App\Models\ExpireSupportDisplay;

use Tests\TestCase;

use function GuzzleHttp\json_decode;

class SettingsControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_saavelicenseId_if_licenseexpireddate_fallsIntoCount()
    {
        $this->withoutMiddleware();
        $data = [
            'count' => 5,
        ];
        $response = $this->call('POST', url('api/admin/save-license-expire-range'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('expire_updates_display', ['license_id' => 2]);
    }
    public function test_getdata_from_expire_updates_displaytable()
    {
        $this->withoutMiddleware();
        $licenseId = ExpireUpdatesDisplay::factory()->create()->license_id;
        $this->assertDatabaseHas('expire_updates_display', ['license_id' => $licenseId]);
        $response = $this->call('GET', url("api/admin/get-updates-expirings"));
        $response->assertStatus(200);
        $this->assertEquals(2, json_decode($response->getContent())->expiring_update[0]->license_id);
    }

    public function test_saavelicenseId_if_supportexpiredate_fallsIntoCount()
    {
        $this->withoutMiddleware();
        $data = [
            'count' => 5,
        ];
        $response = $this->call('POST', url('api/admin/save-support-expire-range'), $data);
        $response->assertStatus(200);
        // dd(json_decode($response->getContent()));
        $this->assertDatabaseHas('expire_support_display', ['license_id' => 2]);
    }
    public function test_getdata_from_expire_support_displaytable()
    {
        $this->withoutMiddleware();
        $licenseId = ExpireSupportDisplay::factory()->create()->license_id;
        $this->assertDatabaseHas('expire_support_display', ['license_id' => $licenseId]);
        $response = $this->call('GET', url("api/admin/get-support-expirings"));
        $response->assertStatus(200);
        $this->assertEquals(2, json_decode($response->getContent())->expiring_support[0]->license_id);
    }
}
