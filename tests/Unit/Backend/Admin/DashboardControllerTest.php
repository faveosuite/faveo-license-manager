<?php

namespace Tests\Unit\Backend\Admin;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\AflProducts;
use App\Models\AfuVersions;
use App\Models\AflInstallations;
use App\Models\AflCallbacks;
use App\Models\AflReports;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function dashboard_returns_expected_counts()
    {
        // Given
        AflProducts::factory()->count(5)->create(['product_status' => '1']);
        AfuVersions::factory()->count(3)->create(['version_status' => '1']);
        AflInstallations::factory()->count(4)->create(['installation_status' => '1']);
        AflCallbacks::factory()->count(2)->create(['callback_status' => '1']);
        AflReports::factory()->count(6)->create(['report_status' => '1']);

        // When
        $response = $this->get('api/admin/dashboarddropdown');

        // Then
        $response->assertStatus(200);
        $response->assertJson([
            'productsCount' => 5,
            'versionsCount' => 3,
            'installationsCount' => 4,
            'callbacksCount' => 2,
            'latestProducts' => AflProducts::latest('product_date')->take(10)->get()->toArray(),
            'latestVersions' => AfuVersions::latest('version_date')->take(10)->get()->toArray(),
            'latestInstallations' => AflInstallations::latest('installation_date')->take(10)->get()->toArray(),
            'latestCallbacks' => AflCallbacks::latest('callback_date_time')->take(10)->get()->toArray(),
            'latestReports' => AflReports::latest('report_date_time')->take(10)->get()->toArray(),
        ]);
    }

    public function dashboard_returns_no_data_when_no_active_entities()
    {
        // Given
        AflProducts::factory()->count(5)->create(['product_status' => '0']);
        AfuVersions::factory()->count(3)->create(['version_status' => '0']);
        AflInstallations::factory()->count(4)->create(['installation_status' => '0']);
        AflCallbacks::factory()->count(2)->create(['callback_status' => '0']);
        AflReports::factory()->count(6)->create(['report_status' => '0']);

        // When
        $response = $this->get('api/admin/dashboarddropdown');

        // Then
        $response->assertStatus(200);
        $response->assertJson([
            'productsCount' => 0,
            'versionsCount' => 0,
            'installationsCount' => 0,
            'callbacksCount' => 0,
            'latestProducts' => [],
            'latestVersions' => [],
            'latestInstallations' => [],
            'latestCallbacks' => [],
            'latestReports' => [],
        ]);
    }
}
