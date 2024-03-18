<?php

namespace Tests\Unit\Backend\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_dashboard()
    {
        $this->withoutMiddleware();
        $aflInstallationsId = AflInstallations::factory()->create()->installation_id;
        $aflCallbacksId = AflCallbacks::factory()->create()->callback_id;
        $afuCallbacksId = AfuCallbacks::factory()->create()->callback_id;
        $aflReportsId = AflReports::factory()->create()->report_id;
        $aflLicensesId = AflLicenses::factory()->create()->license_id;
        $result = $this->assertDatabaseHas('afl_installations', ['installation_id' => $aflInstallationsId]);
        $this->assertDatabaseHas('afl_callbacks', ['callback_id' => $aflCallbacksId]);
        $this->assertDatabaseHas('afu_callbacks', ['callback_id' => $afuCallbacksId]);
        $this->assertDatabaseHas('afl_reports', ['report_id' => $aflReportsId]);
        $this->assertDatabaseHas('afl_licenses', ['license_id' => $aflLicensesId]);
        $response = $this->call('GET', url("api/admin/dashboarddropdown"));
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(1, $responseContent->data->callbacksCount);
        $this->assertCount(1, $responseContent->data->latestProducts);
        $this->assertCount(0, $responseContent->data->latestVersions);
        $this->assertEquals(100, $responseContent->data->latestInstallations[0]->product_id);
        $this->assertEquals(100, $responseContent->data->latestCallbacks[0]->product_id);
        $this->assertEquals(0, $responseContent->data->latestReports[0]->product_id);
        $this->assertCount(0, $responseContent->data->expiredVersions);

    }
}
