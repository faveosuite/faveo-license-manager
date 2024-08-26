<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflClients;
use App\Models\AflLicenses;
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
        $productsCount = AflProducts::count();

        // Count of distinct active version numbers
        $versionsCount = AfuVersions::distinct('version_number')->count('version_number');

        //Count of active license
        $licenseCount = AflLicenses::count();

        // Count of distinct callbacks
        $callbacksCount = bcadd(AflCallbacks::count(), AfuCallbacks::count());

        // Latest products
        $latestProducts = AflProducts::where('product_status', '1')
            ->withCount(['licenses', 'installations'])
            ->orderBy('product_id','desc')
            ->take(10)
            ->get()
            ->transform(function ($product) {
                $product->versions = $product->product_latest_version;
                $product->versions_count = $product->product_version_count;
                return $product;
            });

        // Latest versions
        $latestVersions = AfuVersions::where('version_status', '1')->with('product:product_id,product_title')->distinct('version_number')->orderByDesc('version_date')->orderByDesc('version_id')->take(10)->get();

        // Latest installations (AFL and AFU combined)
        $latestInstallations = AflInstallations::with('license:license_id,license_code')->where('installation_status', '1')->orWhere('installation_status', '1')->distinct('installation_ip')->orderByDesc('installation_date')->orderByDesc('installation_id')->take(10)->get();

        // Latest callbacks (AFL and AFU combined)
        $latestCallbacks = AflCallbacks::where('callback_status', '1')->orWhere('callback_status', '1')->distinct('callback_ip')->orderByDesc('callback_date_time')->orderByDesc('callback_id')->take(10)->get();

        // Latest product reports
        $latestReports = AflReports::with('license:license_id,license_code')->where('report_status', '1')->orderByDesc('report_date_time')->take(10)->get();

        $currentDateTime = Carbon::now()->toDateTimeString();

        // Expired versions
        $expiredVersions = AfuVersions::where('expired', '1')->take(10)->get();

        //Latest clients
        $latestClients = AflClients::select('client_id','client_email','client_active_date','client_status')->withCount('license')->fullName()->where('client_status', '1')->orderByDesc('client_active_date')->orderByDesc('client_id')->take(10)->get();

        //Latest licenses
        $latestLicenses = AflLicenses::with('product:product_id,product_title','clients:client_id,client_email')->select('license_id','client_id','product_id','license_code','license_date','license_status')->withClientEmailOrLicenseCode()->where('license_status', '1')->orderByDesc('license_date')->orderByDesc('license_id')->take(10)->get();

        //Expiring support
        $expiringSupport = AflLicenses::with('product:product_id,product_title','clients:client_id,client_email')->withClientEmailOrLicenseCode()->select('license_id','client_id','product_id','license_code','license_date','license_support_date','license_status')->where('license_status',1)->where('license_support_date', '>', $currentDateTime)->orderBy('license_support_date')->orderBy('license_id')->take(10)->get();

        //Expiring updates
        $expiringUpdates = AflLicenses::with('product:product_id,product_title','clients:client_id,client_email')->withClientEmailOrLicenseCode()->select('license_id','client_id','product_id','license_code','license_date','license_updates_date','license_status')->where('license_status',1)->where('license_updates_date', '>', $currentDateTime)->orderBy('license_updates_date')->orderBy('license_id')->take(10)->get();

        return successResponse(Lang::get('lang.dashboard_show'), compact('productsCount', 'versionsCount','licenseCount', 'callbacksCount', 'latestProducts', 'latestVersions', 'latestInstallations', 'latestCallbacks', 'latestReports', 'expiredVersions','latestClients','latestLicenses','expiringSupport','expiringUpdates'));
    }
}

