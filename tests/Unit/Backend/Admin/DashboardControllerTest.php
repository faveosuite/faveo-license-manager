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
    // use RefreshDatabase;
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
        $response->assertStatus(200);
        $this->assertEquals(2, json_decode($response->getContent())->data->callback_count);
        $this->assertEquals('Helpdesk Product 2', json_decode($response->getContent())->data->latest_products[0]->product_title);
        $this->assertEquals(100, json_decode($response->getContent())->data->afl_latest_installation[0]->product_id);
        $this->assertEquals(100, json_decode($response->getContent())->data->afl_latest_callbacks[0]->product_id);
        $this->assertEquals(100, json_decode($response->getContent())->data->afu_latest_callbacks[0]->product_id);
        $this->assertEquals(0, json_decode($response->getContent())->data->latest_product_reports[0]->product_id);
    }
}