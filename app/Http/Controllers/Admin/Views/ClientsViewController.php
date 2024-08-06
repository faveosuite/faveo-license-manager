<?php

namespace App\Http\Controllers\Admin\Views;

use App\Http\Controllers\Controller;
use App\Models\AflClients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class ClientsViewController extends Controller
{
    public function getClientInfo($client_id)
    {
        $client = AflClients::select('client_id','client_email','client_role','client_active_date','client_profile_pic','client_address','client_organization','client_status')
            ->fullName()->find($client_id);
        return successResponse(Lang::get('lang.client_details'), $client);
    }
    public function getClientInstallations(Request $request,$client_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'installation_id');
        $clientInstallations = AflClients::find($client_id)
            ->installation()
            ->with('product:product_id,product_title')
            ->select('installation_id','client_id','installation_domain','installation_ip','installation_date','installation_status')
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('installation_domain', 'like', '%' . $searchQuery . '%')
                        ->orWhere('installation_ip', 'like', '%' . $searchQuery . '%')
                        ->orWhere('installation_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('installation_date', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse(Lang::get('lang.client_installations'), $clientInstallations);
    }
    public function getClientLicenses(Request $request,$client_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'license_id');
        $productLicenses = AflClients::find($client_id)
            ->license()
            ->with('product:product_id,product_title')
            ->select('license_id','product_id','client_id','license_code','license_order_number','license_date','license_expire_date','license_updates_date','license_support_date','license_status')
            ->withAggregate('product as product_title','product_title')
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('license_date', 'like', '%' . $searchQuery . '%')
                        ->orWhere('license_expire_date', 'like', '%' . $searchQuery . '%')
                        ->orWhere('license_updates_date', 'like', '%' . $searchQuery . '%')
                        ->orWhere('license_support_date', 'like', '%' . $searchQuery . '%')
                        ->orWhere('license_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        $productLicenses->getCollection()->transform(function ($license) {
            $license->latest_call_backs = $license->latest_call_back;
            $license->installation_counts = $license->installation_count;
            $license->license_order_url = $license->order_url;
            return $license;
        });
        return successResponse(Lang::get('lang.client_licenses'), $productLicenses);
    }
}
