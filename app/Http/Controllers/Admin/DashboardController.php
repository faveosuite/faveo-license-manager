<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflProducts;
use App\Models\AfuVersions;
use App\Models\AflInstallations;
use App\Models\AfuInstallations;
use App\Models\AflCallbacks;
use App\Models\AfuCallbacks;
use App\Models\AflReports;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Count of active products
        $productsCount = AflProducts::where('product_status', '1')->count();

        // Count of distinct active version numbers
        $versionsCount = AfuVersions::where('version_status', '1')->distinct('version_number')->count('version_number');

        // Count of distinct installations
        $installationsCount = AflInstallations::where('installation_status', '1')->orWhere('installation_status', '1')->distinct('installation_ip')->count('installation_ip');

        // Count of distinct callbacks
        $callbacksCount = AflCallbacks::where('callback_status', '1')->orWhere('callback_status', '1')->distinct('callback_ip')->count('callback_ip');

        // Latest products
        $latestProducts = AflProducts::where('product_status', '1')->orderByDesc('product_date')->take(10)->get();

        // Latest versions
        $latestVersions = AfuVersions::where('version_status', '1')->distinct('version_number')->orderByDesc('version_date')->take(10)->get();

        // Latest installations (AFL and AFU combined)
        $latestInstallations = AflInstallations::where('installation_status', '1')->orWhere('installation_status', '1')->distinct('installation_ip')->orderByDesc('installation_date')->take(10)->get();

        // Latest callbacks (AFL and AFU combined)
        $latestCallbacks = AflCallbacks::where('callback_status', '1')->orWhere('callback_status', '1')->distinct('callback_ip')->orderByDesc('callback_date_time')->take(10)->get();

        // Latest product reports
        $latestReports = AflReports::where('report_status', '1')->orderByDesc('report_date_time')->take(10)->get();

        // Expired versions
        $currentDateTime = Carbon::now()->toDateTimeString();
        AfuVersions::where('version_expire_date', '>', $currentDateTime)->update(['expired' => '0']);
        AfuVersions::where('version_expire_date', '<=', $currentDateTime)->update(['expired' => '1']);
        $expiredVersions = AfuVersions::where('expired', '1')->get();

        return successResponse(Lang::get('lang.Product_Show'), compact('productsCount', 'versionsCount', 'installationsCount', 'callbacksCount', 'latestProducts', 'latestVersions', 'latestInstallations', 'latestCallbacks', 'latestReports', 'expiredVersions'));
    }
}

