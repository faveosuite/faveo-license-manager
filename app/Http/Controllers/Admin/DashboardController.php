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
use App\Models\AflLicenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Carbon;

class DashboardController extends Controller
{
    public function orderslink()
    {
        $number = "10000005";
        $data = ['message' => 'Hello from the Laravel API!'];
        return response()->json($number);

    }
    public function dashboard()
    {
        $productscount = AflProducts::where('product_status', '1')->count();
        $versionscount = AfuVersions::where('version_status', '1')->distinct('version_number')->count('version_number');
        $aflinsatallations = AflInstallations::distinct('installation_ip')->count('installation_ip');
        $afuinsatallations = AfuInstallations::distinct('installation_ip')->count('installation_ip');
        $aflcallbacks = AflCallbacks::distinct('callback_ip')->count('callback_ip');
        $afucallbacks = AfuCallbacks::distinct('callback_ip')->count('callback_ip');


        $latestproducts = AflProducts::where('product_status', '1')->orderBy('product_date', 'desc')->take(10)->get();
        $latestversions = AfuVersions::where('version_status', '1')->distinct('version_number')->orderBy('version_date', 'desc')->take(10)->get();
        $afllatestinsatallations = AflInstallations::where('installation_status', '1')->distinct('installation_ip')->orderBy('installation_date', 'desc')->take(10)->get();
        $afulatestnsatallations = AfuInstallations::where('installation_status', '1')->distinct('installation_ip')->orderBy('installation_date', 'desc')->take(10)->get();
        $afllatestcallbacks = AflCallbacks::where('callback_status', '1')->distinct('callback_ip')->orderBy('callback_date_time', 'desc')->take(10)->get();
        $afulatestcallbacks = AfuCallbacks::where('callback_status', '1')->distinct('callback_ip')->orderBy('callback_date_time', 'desc')->take(10)->get();
        $afllatestreports = AflReports::where('report_status', '1')->orderBy('report_date_time', 'desc')->take(10)->get(); //need to check distinct requirement for product_id

        $installations = $aflinsatallations + $afuinsatallations;
        $callbacks = $aflcallbacks + $afucallbacks;

        $currentdate = Carbon\Carbon::now();
        $currentdate = $currentdate->toDateTimeString();
        $versionsexpiredate = AfuVersions::where('version_status', '1')->distinct('version_number')->get('version_expire_date');
        AfuVersions::where('version_expire_date', '>', $currentdate)->update(['expired' => '0']);
        AfuVersions::where('version_expire_date', '<=', $currentdate)->update(['expired' => '1']);
        $expiredversions = AfuVersions::where('expired', '1')->get();
        return successResponse(Lang::get('lang.Product_Show'), [
            'products_count' => $productscount, 'version_count' => $versionscount, 'installlation_count' => $installations, 'callback_count' => $callbacks, 'latest_products' => $latestproducts, 'latest_versions' => $latestversions, 'afl_latest_installation' => $afllatestinsatallations, 'afu_latest_installation' => $afulatestnsatallations,
            'afl_latest_callbacks' => $afllatestcallbacks, 'afu_latest_callbacks' => $afulatestcallbacks, 'latest_product_reports' => $afllatestreports, 'expired_versions' =>  $expiredversions
        ]);
    }
}
