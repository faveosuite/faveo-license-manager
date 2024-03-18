<?php

namespace App\Http\Controllers\Admin\Views;

use App\Http\Controllers\Controller;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class InstallationViewController extends Controller
{
    public function getInstallation($installation_id)
    {
        $installation = AflInstallations::with('product:product_id,product_title','clients:client_id,client_email','license:license_id,license_code')
            ->find($installation_id);
        return successResponse(Lang::get('lang.installation_details'),$installation);
    }
    public function getInstallationCallbacks(Request $request,$installation_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'callback_id');
        $installationDomain = AflInstallations::where('installation_id',$installation_id)->value('installation_domain');
        $callbacks = AflCallbacks::where('callback_domain', $installationDomain)
            ->select('callback_id','callback_ip','callback_domain','callback_date_time','callback_status')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                 $query->where('callback_ip', 'like', '%'.$searchQuery.'%')
                     ->orWhere('callback_domain', 'like', '%'.$searchQuery.'%')
                     ->orWhere('callback_date_time', 'like', '%'.$searchQuery.'%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse(Lang::get('lang.installation_callbacks'), $callbacks);
    }
}
