<?php

namespace App\Http\Controllers\Admin\Views;

use App\Http\Controllers\Controller;
use App\Models\AflInstallations;
use App\Models\AfuVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class VersionsViewController extends Controller
{
    public function getVersionInfo($version_id)
    {
       $version = AfuVersions::with('product:product_id,product_title')
           ->find($version_id);
       return successResponse(Lang::get('lang.version_details'),$version);
    }
    public function getVersionCallbacks(Request $request,$version_id)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'callback_id');
        $versionInstallation = AfuVersions::find($version_id)
            ->callback()
            ->select('callback_id','version_id','callback_ip','callback_date_time','callback_status','callback_type')
            ->withAggregate(['types as callback_type'],'value')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('callback_ip', 'like', '%' . $searchQuery . '%')
                        ->orWhere('callback_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('callback_date_time', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);;
        return successResponse(Lang::get('lang.version_callbacks'),$versionInstallation);
    }
}
