<?php

namespace Tests\Unit\Backend\Admin;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\AflProducts;
use App\Models\AfuVersions;
use App\Models\AflInstallations;
use App\Models\AfuInstallations;
use App\Models\AflCallbacks;
use App\Models\AfuCallbacks;
use App\Models\AflReports;
use App\Models\AflLicenses;
use Tests\TestCase;


class DashboardControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_dashboard()
    {
        $this->withoutMiddleware();
        $licenseCode = \Str::random(16);
        $aflInstallationsId = AflInstallations::factory()->create(['product_id' => 100, 'license_code' => $licenseCode, 'installation_status' => 1, 'installation_date' => now()])->installation_id;
        $aflCallbacksId = AflCallbacks::factory()->create(['product_id' => 100, 'license_code' => $licenseCode])->callback_id;
        $afuCallbacksId = AfuCallbacks::factory()->create(['callback_id' => rand(100000, 999999)])->callback_id;
        $aflReportsId = AflReports::factory()->create()->report_id;
        $aflLicensesId = AflLicenses::factory()->create(['license_id' => rand(100000, 999999), 'license_code' => $licenseCode])->license_id;
        $this->assertDatabaseHas('afl_installations', ['installation_id' => $aflInstallationsId]);
        $this->assertDatabaseHas('afl_callbacks', ['callback_id' => $aflCallbacksId]);
        $this->assertDatabaseHas('afu_callbacks', ['callback_id' => $afuCallbacksId]);
        $this->assertDatabaseHas('afl_reports', ['report_id' => $aflReportsId]);
        $this->assertDatabaseHas('afl_licenses', ['license_id' => $aflLicensesId]);
        $response = $this->call('GET', url("api/admin/dashboarddropdown"));
        $responseContent = json_decode($response->getContent());
        $this->assertEquals(100, $responseContent->data->latestInstallations[0]->product_id);
        $this->assertEquals(100, $responseContent->data->latestCallbacks[0]->product_id);
        $this->assertCount(0, $responseContent->data->expiredVersions);
    }
}
