<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AfuVersions;
use Illuminate\Http\Request;

class VersionsController extends Controller
{
    public function show(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','version_id');
        $versions = AfuVersions::with('product:product_id,product_title')
            ->select('version_id','product_id','version_number','version_date','version_upgrade_count','version_status')
            ->withAggregate(['product as product_title'], 'product_title')
            ->withCount('callback')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('version_number', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('version_date', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('version_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhereHas('product', function ($q) use ($searchQuery) {
                            $q->where('product_title', 'LIKE', '%' . $searchQuery . '%');
                        });
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse('',$versions);
    }
}
