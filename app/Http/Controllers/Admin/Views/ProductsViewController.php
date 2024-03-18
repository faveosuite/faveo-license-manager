<?php

namespace App\Http\Controllers\Admin\Views;

use App\Http\Controllers\Controller;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\AfuVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class ProductsViewController extends Controller
{
    public function getProductDetails($id)
    {
        $product = AflProducts::select('product_id', 'product_title', 'product_sku', 'product_url_homepage', 'product_url_download', 'product_status')
            ->with(['versions' => function($query) {
                $query->select('version_id','product_id','version_number')->latest()->first();
            }])
            ->find($id);
        return successResponse(Lang::get('lang.product_details'),$product);
    }
    public function getProductInstallations(Request $request, $id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'installation_id');
        $productInstallations = AflProducts::find($id)
            ->installations()
            ->with('clients:client_id,client_email','license:license_id,license_code')
            ->select('installation_id','product_id','client_id','license_code','installation_domain','installation_ip','installation_date','installation_status')
            ->withAggregate('clients as client_email','client_email')
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->whereHas('clients', function ($query) use ($searchQuery) {
                        $query->where('client_email', 'like', '%' . $searchQuery . '%');
                    })
                        ->orWhere('license_code', 'like', '%' . str_replace("-", "",$searchQuery)  . '%')
                        ->orWhere('installation_domain', 'like', '%' . $searchQuery . '%')
                        ->orWhere('installation_ip', 'like', '%' . $searchQuery . '%')
                        ->orWhere('installation_date', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.product_installations'), $productInstallations);
    }

    public function getProductLicenses(Request $request, $id){
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'license_id');

        $productLicenses = AflProducts::find($id)
            ->licenses()
            ->with('clients:client_id,client_email')
            ->select('license_id','product_id','client_id','license_code','license_order_number','license_date','license_expire_date','license_updates_date','license_support_date','license_status')
            ->withAggregate('clients as client_email','client_email')
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->whereHas('clients', function ($query) use ($searchQuery) {
                        $query->where('client_email', 'like', '%' . $searchQuery . '%');
                    })
                        ->orWhere('license_code', 'like', '%' . str_replace("-", "",$searchQuery)  . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        $productLicenses->getCollection()->transform(function ($license) {
            $license->installation_counts = $license->installation_count;
            $license->latest_call_backs = $license->latest_call_back;
            $license->license_order_url = $license->order_url;
            return $license;
        });
        return successResponse(Lang::get('lang.product_licenses'), $productLicenses);
    }
    public function getProductVersions(Request $request, $productId)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'version_id');
        $productVersions = AflProducts::find($productId)
            ->versions()
            ->select('version_id','product_id','version_number','version_date','version_upgrade_count','version_status')
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where('version_number', 'like', '%' . $searchQuery . '%')
                    ->orWhere('version_date', 'like', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse(Lang::get('lang.product_versions'), $productVersions);
    }
}
