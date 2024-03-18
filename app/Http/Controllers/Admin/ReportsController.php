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
            ->withUserFormatted()
            ->where('report_system', 1)
            ->whereHas('user', function ($query) {
                $query->where('client_role', 'admin');
            });
        $rawSql = $reportsQuery->toSql();
        $reportsQuery = DB::table(DB::raw("({$rawSql}) as sub"))
            ->mergeBindings($reportsQuery->getQuery())
            ->where('report_text', 'like', '%' . $searchQuery . '%')
            ->orWhere('user_formatted', 'like', '%' . $searchQuery . '%')
            ->orderBy($sortField, $sortOrder);

        $paginatedReports = $reportsQuery->paginate($perPage, ['*'], 'page', $page);

        // Modify the fetched data as needed
        $modifiedReports = $paginatedReports->map(function ($report) {
            // report_date_time, and report_status
            $report->report_date_time = removeSeconds($report->report_date_time);
            $report->report_status_formatted = returnFormattedReportStatusArray($report->report_status);

            return $report;
        });
        $paginatedReports->setCollection($modifiedReports);
        return successResponse(Lang::get('lang.SystemReport_Show'), $paginatedReports,200);
    }

    public function reportArrayCracking(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','report_id');

        // Fetch paginated cracking reports with related user and product data
        $paginatedReports = AflReports::with(['user:client_id,client_email,client_fname,client_lname,client_role', 'product'])
            ->where('account_id', 0)
            ->where('product_id', 0)
            ->where('report_system', 0)
            ->where(function ($query) use ($searchQuery) {
                    $query->where('report_text', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('license_code', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        // Modify the fetched data as needed
        $modifiedReports = $paginatedReports->map(function ($report) {
            // Format client, report_date_time, and report_status
            $report->client_formatted = formatClient($report->license_code, optional($report->user)->client_email);
            $report->report_date_time = removeSeconds($report->report_date_time);
            $report->report_status_formatted = returnFormattedReportStatusArray($report->report_status);

            return $report;
        });

        $paginatedReports->setCollection($modifiedReports);
        return successResponse(Lang::get('lang.CrackingReport_Show'), $paginatedReports,200);
    }

    public function reportArrayLicense(Request $request)
    {
        $perPage = $request->input('perPage',10); // Number of items per page
        $page = $request->input('page', 1); // Get the current page from the request
        $searchQuery = str_replace("-","",$request->input('search_query'));
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','report_id');

        // Fetch paginated license reports with related user and product data
        $paginatedReports = AflReports::with(['user:client_id,client_email,client_fname,client_lname,client_role', 'product'])
            ->withAggregate('product as product_title','product_title')
            ->whereNotNull('license_code')
            ->where(function ($query) use ($searchQuery) {
                $query->whereHas('product', function ($query) use ($searchQuery) {
                    $query->where('product_title', 'LIKE', '%' . $searchQuery . '%');
                })
                    ->orWhere('report_text', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('license_code', 'LIKE', '%' . $searchQuery . '%');
            })
                ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['afl_reports.*'], 'page', $page);



        // Modify the fetched data as needed
        $modifiedReports = $paginatedReports->map(function ($report) {
            // Format product, client, report_date_time, and report_status
            $report->products = $report->product ? $report->product->product_title : '---';
            $report->client_formatted = formatClient($report->license_code, optional($report->user)->client_email);
            $report->report_date_time = removeSeconds($report->report_date_time);
            $report->report_status_formatted = returnFormattedReportStatusArray($report->report_status);

            return $report;
        });

        $paginatedReports->setCollection($modifiedReports);
        return successResponse(Lang::get('lang.LicenseReport_Show'), $paginatedReports,200);
    }

    public function reportArrayUpdate()
    {
        $perPage = 10; // Number of items per page
        $page = request()->input('page', 1); // Get the current page from the request

        // Fetch paginated update reports with related product data
        $paginatedReports = AflReports::with('product')
            ->orderByDesc('report_date_time')
            ->orderByDesc('report_id')
            ->paginate($perPage, ['*'], 'page', $page);

        // Modify the fetched data as needed
        $modifiedReports = $paginatedReports->map(function ($report) {
            // Format product_title, report_date_time, and report_status
            $report->product_title = $report->product ? $report->product->product_title : 'Unknown';
            $report->report_date_time = removeSeconds($report->report_date_time);
            $report->report_status_formatted = returnFormattedReportStatusArray($report->report_status);

            return $report;
        });

        return $modifiedReports->toArray();
    }
}



