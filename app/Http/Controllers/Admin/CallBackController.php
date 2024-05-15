<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflCallbacks;
use App\Models\AfuCallbacks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class CallBackController extends Controller
{
    public function licneseCallbacks(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = str_replace("-","",$request->input('search_query'));
        $sortOrder= $request->input('sort_order') ? $request->input('sort_order') : 'desc';
        $sortField =$request->input('sort_field') ? $request->input('sort_field') :'callback_id';

        // Fetch paginated callbacks with related product and user data using Eloquent relationships
        $paginatedCallbacks = AflCallbacks::with(['product', 'user'])
            ->select('afl_callbacks.*')
            ->leftJoin('afl_products', 'afl_callbacks.product_id', '=', 'afl_products.product_id')
            ->where(function ($query) use ($searchQuery) {
                $query->whereHas('product', function ($query) use ($searchQuery) {
                    $query->where('product_title', 'LIKE', '%'.$searchQuery.'%');
                })->orWhere('license_code', 'LIKE', '%'.$searchQuery.'%')
                    ->orWhere('callback_ip', 'LIKE', '%'.$searchQuery.'%')
                    ->orWhere('callback_domain', 'LIKE', '%'.$searchQuery.'%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        // Modify the fetched data as needed
        $modifiedCallbacks = $paginatedCallbacks->map(function ($callback) {
            // Format client and callback status
            $callback->client_formatted = formatClient($callback->license_code, optional($callback->user)->client_email);
            $callback->callback_date_time = removeSeconds($callback->callback_date_time);
            $callback->callback_status_formatted = returnFormattedStatusArray($callback->callback_status, 'Success', 'Error', 'Unknown');

            return $callback;
        });
        $paginatedCallbacks->setCollection($modifiedCallbacks);
        return successResponse(Lang::get('lang.Callback_Show'),$paginatedCallbacks,200);
    }

    public function updateCallbacks()
    {
        $callbacks = $this->callbackUpdateArray();

        return $callbacks;
    }

    private function callbackUpdateArray()
    {
        $rows_array = AfuCallbacks::leftJoin('afl_products', 'afu_callbacks.product_id', '=', 'afl_products.product_id')
                               ->leftJoin('afu_versions', 'afu_callbacks.version_id', '=', 'afu_versions.version_id')
                               ->orderBy('afu_callbacks.callback_date_time', 'DESC')
                                ->orderBy('afu_callbacks.callback_id', 'DESC')->get()->toArray();

        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['callback_date_time'] = removeSeconds($item_array['callback_date_time']);
            $item_array['callback_type_formatted'] = $this->returnFormattedCallbackTypeArray($item_array['callback_type']);
            $item_array['callback_status_formatted'] = returnFormattedStatusArray($item_array['callback_status'], 'Success', 'Error', 'Unknown');

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //format and return callback type text
    private function returnFormattedCallbackTypeArray($callback_type)
    {
        $callback_type_formatted = '';

        if ($callback_type == 1) {
            $callback_type_formatted = 'Version Check';
        } elseif ($callback_type == 2) {
            $callback_type_formatted = 'Installation';
        } elseif ($callback_type == 3) {
            $callback_type_formatted = 'Upgrade';
        } else {
            $callback_type_formatted = 'Unknown';
        }

        return $callback_type_formatted;
    }

    public function callbacksDelete(Request $request)
    {
        $removed_records = 0;
        $error_details = '';
        $action_success = 0;
        $callback_ids_array = $request->call;
        $isLicense = $request->get('isLicense');
        if (! empty($callback_ids_array)) {
            foreach ($callback_ids_array as $callback_id) {
                $removed_records += $this->deleteCallback($callback_id, $isLicense);
            }
            if (! aflValidateIntegerValue($removed_records)) {
                $error_details .= 'Invalid record or database error.';
            } else {
                $action_success = 1;
            }
        } else {
            $error_details .= 'No record selected.';
        }
        if ($action_success == 1) { //everything OK
            $page_message = "Deleted $removed_records callback(s).";
        } else { //display error message
            $page_message = "Callback could not be deleted because of this reason: $error_details";
        }

        createReport(strip_tags($page_message), 1, 1, $action_success);

        return successResponse($page_message, $removed_records, 200);
    }

    //delete callback
    private function deleteCallback($callback_id, $isLicense)
    {
        $removed_records = 0;
        if ($isLicense) {
            if (aflValidateIntegerValue($callback_id)) {
                $removed_records += AflCallbacks::where('callback_id', $callback_id)->delete(); //doMysqlQuery("DELETE FROM apl_callbacks WHERE callback_id=?", array($callback_id), array("i"));
            }

            return $removed_records;
        }
        if (aflValidateIntegerValue($callback_id)) {
            $removed_records += AfuCallbacks::where('callback_id', $callback_id)->delete();
        }

        return $removed_records;
    }
}
