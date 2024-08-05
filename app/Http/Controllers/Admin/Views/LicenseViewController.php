<?php

namespace App\Http\Controllers\Admin\Views;

use App\Http\Controllers\Controller;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\InstallationLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class LicenseViewController extends Controller
{
    public function getLicenseDetails($license_id)
    {
        $license = AflLicenses::with('product:product_id,product_title','clients:client_id,client_email')->select(
            'license_id',
            'product_id',
            'client_id',
            'license_ip',
            'license_code',
            'license_limit',
            'license_expire_date',
            'license_support_date',
            'license_order_number',
            'license_domain',
            'license_date',
            'license_updates_date',
            'license_status'
        )
            ->find($license_id);
        $license->license_order_url = $license->order_url;
        $license->installation_counts = $license->installation_count;
        $license->latest_call_backs = $license->latest_call_back;
        $license->call_backs_count = $license->call_backs->count();
        return successResponse(Lang::get('lang.license_details'),$license);
    }
    public function getLicenseInstallations(Request $request,$license_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'installation_id');

        $license = AflLicenses::find($license_id);
        $licenseInstallations = AflInstallations::where('product_id',$license->product_id)
            ->select('installation_id','client_id','product_id','installation_domain','installation_ip','installation_date','installation_status')
            ->when($license->license_code,function ($query) use ($license){
                $query->where('license_code', $license->license_code);
            })
            ->when($license->client_id,function ($query) use ($license){
                $query->where('client_id', $license->client_id);
            })
            ->when($searchQuery,function ($query,$searchQuery){
                $query->where(function ($query) use ($searchQuery) {
                    $query->Where('installation_domain', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('installation_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('installation_date', 'LIKE', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse(Lang::get('lang.license_installations'),$licenseInstallations);
    }
    public function getLicenseCallBacks(Request $request,$license_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'callback_id');
        $license = AflLicenses::find($license_id);
        $licenseCallBacks = AflCallbacks::where('product_id',$license->product_id)
        ->where('client_id',$license->client_id)
        ->Where('license_code',$license->license_code)
            ->when($searchQuery,function ($query,$searchQuery){
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('callback_domain', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('callback_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('callback_date_time', 'LIKE', '%' . $searchQuery . '%');
                });
            })
        ->orderBy($sortField, $sortOrder)
        ->paginate($perPage, ['*'], 'page', $page);
        return successResponse(Lang::get('lang.license_callback'),$licenseCallBacks);
    }
    public function getLicenseInstallationLogs(Request $request,$license_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'id');
        $licenseDomains = $this->getLicenseInstallations($request,$license_id);
        $installations = json_decode($licenseDomains->getContent())->data->data;
        $domains = array_column($installations, 'installation_domain');
        $installationLogs = InstallationLogs::with('version:version_id,version_number')->whereIn('installation_domain', $domains)
            ->when($searchQuery,function ($query,$searchQuery){
                $query->where('installation_domain', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('installation_ip', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse('',$installationLogs);
    }
}
