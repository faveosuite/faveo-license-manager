<?php

namespace App\Http\Controllers\Admin;


use App\Exceptions\NonLoggableException;
use App\Models\ExceptionLog;
use App\Models\LogCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Handles all write related operations while logging
 * NOTE: while passing any category, please make sure that this category exists in LogSeeder.php
 *
 * @author sandesh menath <sandesh.menath@ladybirdweb.com>
 */
class LogWriteController
{
    /**
     * Logs exception along with trace
     *
     * @param  Exception|object  $e exception
     * @param  string  $category category to which it belongs
     * @return null
     */
    public function exception($e, $category = 'default')
    {
        try {
            if (! ($e instanceof NonLoggableException)) {
                $category = LogCategory::FirstOrCreate(['name' => $category]);

                return $category->exception()->create([
                    'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
                    'trace' => nl2br($e->getTraceAsString()),
                ]);
            }
        } catch(\Exception $e) {
            // ignore exception
            // Most probably this scenario will not arrive but just for fallback, since this might result is auto-update failure
        }
    }

    public function getExceptionLogs(Request $request)
    {
        $perPage = $request->input('perPage', 15);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order') ? $request->input('sort_order') : 'desc';
        $sortField = $this->getSortField($request->input('sort_field'));
        $logs = ExceptionLog::leftJoin('log_categories', 'log_categories.id', '=', 'exception_logs.log_category_id')
            ->where('exception_logs.created_at', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere(function ($query) use ($searchQuery) {
                $query->where('log_categories.name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('exception_logs.file', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('exception_logs.line', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('exception_logs.trace', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('exception_logs.message', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortOrder)
            ->select('exception_logs.*', 'log_categories.name')
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse('',$logs);
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
