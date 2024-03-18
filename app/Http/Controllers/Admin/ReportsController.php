<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\AflReports;
use App\Models\AflProducts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;


class ReportsController extends Controller
{
    public $error_details = '';


    public $removed_records = 0;


    public $action_success = 1;


    public function reports(Request $request)
    {
        $report_ids_array = $request->arr;
        $whichReport = $request->get('which_report');
        if (! empty($report_ids_array)) {
            foreach ($report_ids_array as $report_id) {
                $this->removed_records += $this->deleteReport($report_id, $this->removed_records);
            }
            if (! aflValidateIntegerValue($this->removed_records)) {
                $this->action_success = 0;
                $this->error_details .=  Lang::get('lang.inavalid_records');
            } else {
                $this->action_success = 1;
            }
        } else {
            $this->action_success = 0;
            $this->error_details .= Lang::get('lang.no_record_selected');
        }
        $page_message = $this->whichReportDeleted($whichReport, $this->action_success, $this->removed_records, $this->error_details);
        createReport(strip_tags($page_message), 1, 1, $this->action_success);


        return response(['message' => $page_message]);
    }


    protected function whichReportDeleted($whichReport, $action_success, $removed_records, $error_details = '')
    {
        if (! empty($whichReport)) {
            if ($action_success == 1) { // everything OK
                $page_message = trans('lang.Deleted', ['removed' => $removed_records, 'which' => $whichReport]);
            } else { // display error message
                $page_message = trans('lang.report_not_deleted', ['which' => $whichReport, 'error_details' => $error_details]);
            }
        }


        return $page_message;
    }


//delete report
    private function deleteReport($report_id, $removed_records)
    {
        if (aflValidateIntegerValue($report_id)) {
            $removed_records += AflReports::where('report_id', $report_id)->delete();
        }


        return $removed_records;
    }


//system1
    public function reportArraySystem(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','report_id');

        // Fetch paginated system reports with related user and product data
        $reportsQuery = AflReports::with(['user'])
            ->select("afl_reports.*")
            ->withUserFormatted()
            ->where('report_system', 1)
            ->when($searchQuery, function ($query, $searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('report_text', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('report_status', 'LIKE', '%' . $this->reportStatusFormatter($searchQuery) . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.SystemReport_Show'), $reportsQuery,200);
    }

    public function reportArrayCracking(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','report_id');

        // Fetch paginated cracking reports with related user and product data
        $crakingReports = AflReports::with(['user:client_id,client_email,client_fname,client_lname,client_role', 'product'])
            ->where('account_id', 0)
            ->where('product_id', 0)
            ->where('report_system', 0)
            ->where(function ($query) use ($searchQuery) {
                    $query->where('report_text', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('report_status', 'LIKE', '%' . $this->reportStatusFormatter($searchQuery) . '%')
                    ->orWhere('license_code', 'LIKE', '%' . str_replace('-','',$searchQuery) . '%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.CrackingReport_Show'), $crakingReports,200);
    }

    public function reportArrayLicense(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','report_id');

        // Fetch paginated license reports with related user and product data
        $LicenseReports = AflReports::with('user:client_id,client_email','license:license_id,license_code')
            ->select('report_id','account_id','report_text','license_code','report_date_time','report_status')
            ->where('license_code' , '!=', null)
            ->withAggregate('user as client_email','client_email')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('report_text', 'like', '%' . $searchQuery . '%')
                        ->orWhere('report_status', 'LIKE', '%' . $this->reportStatusFormatter($searchQuery) . '%')
                        ->orWhereHas('user', function ($query) use ($searchQuery) {
                            $query->where('client_email', 'like', '%' . $searchQuery . '%');
                        })
                        ->orWhere('license_code', 'like', '%' . str_replace("-", "", $searchQuery) . '%')
                        ->orWhere('report_date_time', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.LicenseReport_Show'), $LicenseReports,200);
    }

    public function reportArrayUpdate(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = str_replace("-","",$request->input('search_query'));
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','report_id');

        // Fetch paginated update reports with related product data
        $updateReports = AflReports::with('product')
            ->withAggregate('product','product_title')
            ->where('report_text', 'like', '%' ."upgrade" .'%')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('report_text', 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('product', function ($query) use ($searchQuery) {
                            $query->where('product_title', 'like', '%' . $searchQuery . '%');
                        })
                        ->orWhere('report_status', 'LIKE', '%' . $this->reportStatusFormatter($searchQuery) . '%')
                        ->orWhere('report_date_time', 'like', '%' . $searchQuery . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.report_update'), $updateReports,200);

    }
    private function reportStatusFormatter($status)
    {
        if (strtolower($status) == 'success'){
            $status = 1;
        }
        if (strtolower($status) == 'error' ){
            $status = 0;
        }
        return $status;
    }
}



