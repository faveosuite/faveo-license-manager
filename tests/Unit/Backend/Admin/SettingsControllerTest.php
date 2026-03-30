<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflLicenses;
use App\Models\ExpireUpdatesDisplay;
use App\Models\ExpireSupportDisplay;
use Illuminate\Support\Facades\DB;

use Tests\TestCase;

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
        $license = AflLicenses::factory()->create([
            'license_id' => rand(100000, 999999),
            'license_code' => \Str::random(16),
            'license_expire_date' => now()->addDays(2),
        ]);
        $data = [
            'count' => 5,
        ];
        $response = $this->call('POST', url('api/admin/saveLicenseExpireRange'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('expire_updates_display', ['license_id' => $license->license_id]);
    }
    public function test_getdata_from_expire_updates_displaytable()
    {
        $this->withoutMiddleware();
        $license = AflLicenses::factory()->create([
            'license_id' => rand(100000, 999999),
            'license_code' => \Str::random(16),
        ]);
        ExpireUpdatesDisplay::factory()->create(['license_id' => $license->license_id]);
        $this->assertDatabaseHas('expire_updates_display', ['license_id' => $license->license_id]);
        $response = $this->call('GET', url("api/admin/getUpdatesExpirings"));
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $this->assertNotEmpty($data->expiring_update);
        $licenseIds = array_map(fn($item) => (int) $item->license_id, $data->expiring_update);
        $this->assertContains($license->license_id, $licenseIds);
    }

    public function test_saavelicenseId_if_supportexpiredate_fallsIntoCount()
    {
        $this->withoutMiddleware();
        $license = AflLicenses::factory()->create([
            'license_id' => rand(100000, 999999),
            'license_code' => \Str::random(16),
            'license_support_date' => now()->addDays(2),
        ]);
        $data = [
            'count' => 5,
        ];
        $response = $this->call('POST', url('api/admin/saveSupportExpireRange'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('expire_support_display', ['license_id' => $license->license_id]);
    }
    public function test_getdata_from_expire_support_displaytable()
    {
        $this->withoutMiddleware();
        $license = AflLicenses::factory()->create([
            'license_id' => rand(100000, 999999),
            'license_code' => \Str::random(16),
        ]);
        ExpireSupportDisplay::factory()->create(['license_id' => $license->license_id]);
        $this->assertDatabaseHas('expire_support_display', ['license_id' => $license->license_id]);
        $response = $this->call('GET', url("api/admin/getSupportExpirings"));
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $this->assertNotEmpty($data->expiring_support);
        $licenseIds = array_map(fn($item) => (int) $item->license_id, $data->expiring_support);
        $this->assertContains($license->license_id, $licenseIds);
    }
}
