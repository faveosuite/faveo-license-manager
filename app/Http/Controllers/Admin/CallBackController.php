<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflCallbacks;
use App\Models\AflProducts;
use App\Models\AfuCallbacks;
use App\Models\CallbackTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class CallBackController extends Controller
{
    public function licneseCallbacks(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = str_replace("-","",$request->input('search_query'));
        $sortOrder= $request->input('sort_order','desc');
        $sortField =$request->input('sort_field','callback_id');
        $paginatedCallbacks = AflCallbacks::with(['product:product_id,product_title', 'user:client_id,client_email','license:license_id,license_code'])
            ->select('callback_id','product_id','client_id','license_code','callback_domain','callback_ip','callback_date_time','callback_status')
            ->withAggregate('product as product_title','product_title')
            ->withAggregate(['user as client_email'], 'client_email')
            ->when($searchQuery,function ($query) use ($searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->whereHas('product', function ($query) use ($searchQuery) {
                        $query->where('product_title', 'LIKE', '%' . $searchQuery . '%');
                    })->orWhereHas('user', function ($query) use ($searchQuery) {
                        $query->where('client_email', 'LIKE', '%' . $searchQuery . '%');
                    })
                        ->orWhere('license_code', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('callback_ip', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('callback_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                        ->orWhere('callback_domain', 'LIKE', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.Callback_Show'),$paginatedCallbacks,200);
    }

    public function updateCallbacks(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = str_replace("-","",$request->input('search_query'));
        $sortOrder= $request->input('sort_order','desc');
        $sortField =$request->input('sort_field','callback_id');
       $updateCallbacks = AfuCallbacks::with(['product:product_id,product_title', 'version:version_id,version_number'])
           ->select('callback_id','product_id','version_id','callback_ip','callback_type','callback_date_time','callback_status')
           ->withAggregate('product as product_title','product_title')
           ->withAggregate('types as callback_types','value')
           ->when($searchQuery,function ($query) use ($searchQuery) {
               $query->where(function ($query) use ($searchQuery) {
                   $query->whereHas('product', function ($query) use ($searchQuery) {
                       $query->where('product_title', 'LIKE', '%' . $searchQuery . '%');
                   })
                       ->orWhereHas('version', function ($query) use ($searchQuery) {
                           $query->where('version_number', 'LIKE', '%' . $searchQuery . '%');
                       })
                       ->orWhereHas('types', function ($query) use ($searchQuery) {
                           $query->where('value', 'LIKE', '%' . $searchQuery . '%');
                       })
                       ->orWhere('callback_ip', 'LIKE', '%' . $searchQuery . '%')
                       ->orWhere('callback_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%')
                       ->orWhere('callback_date_time', 'LIKE', '%' . $searchQuery . '%');
               });
           })
           ->orderBy($sortField, $sortOrder)
           ->paginate($perPage, ['*'], 'page', $page);
       return successResponse('',$updateCallbacks);
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
