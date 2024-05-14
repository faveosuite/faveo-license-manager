<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExceptionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class LogViewController extends Controller
{
    public function getExceptionLogs(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order') ? $request->input('sort_order') : 'desc';
        $sortField = $this->getSortField($request->input('sort_field'));
        $logs = ExceptionLog::with('category')
            ->where('created_at', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere(function ($query) use ($searchQuery) {
                $query->whereHas('category', function ($query) use ($searchQuery) {
                    $query->where('name', 'LIKE', '%' . $searchQuery . '%');
                })
                    ->orWhere('file', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('line', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('trace', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('message', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.Exception_Show'),$logs,200);
    }

    private function getSortField($field)
    {
        if(!$field){
            return 'updated_at';
        }

        if($field == 'category'){
            return 'name';
        }

        return $field;
    }
}
