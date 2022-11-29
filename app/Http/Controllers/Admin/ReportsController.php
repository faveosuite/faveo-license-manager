<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $this->error_details .= 'Invalid record or database error.';
            } else {
                $this->action_success = 1;
            }
        } else {
            $this->action_success = 0;
            $this->error_details .= 'No record selected.';
        }
        $page_message = $this->whichReportDeleted($whichReport, $this->action_success, $this->removed_records, $this->error_details);
        createReport(strip_tags($page_message), 1, 1, $this->action_success);

        return response(['message' => $page_message]);
    }

    protected function whichReportDeleted($whichReport, $action_success, $removed_records, $error_details = '')
    {
        if (! empty($whichReport)) {
            if ($action_success == 1) { //everything OK
                $page_message = "Deleted $removed_records $whichReport report(s).";
            } else { //display error message
                $page_message = "$whichReport report(s) could not be deleted because of this reason: $error_details";
            }
        }

        return $page_message;
    }

    //delete report
    private function deleteReport($report_id, $removed_records)
    {
        if (aflValidateIntegerValue($report_id)) {
            $removed_records += DB::table('afl_reports')->where('report_id', $report_id)->delete();
        }

        return $removed_records;
    }

    public function reportArraySystem()
    {
        $rows_array = AflReports::leftJoin('afl_admins', 'afl_reports.account_id', '=', 'afl_admins.admin_id')
                  ->where('report_system', 1)
            ->orderBy('report_date_time', 'DESC')->orderBy('report_id', 'DESC')->get()->toArray();
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['user_formatted'] = $this->formatSystemReportUser($item_array['admin_fname'], $item_array['admin_lname'], $item_array['admin_email']);
            $item_array['report_date_time'] = removeSeconds($item_array['report_date_time']);
            $item_array['report_status_formatted'] = returnFormattedReportStatusArray($item_array['report_status']);

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    public function reportArrayCracking()
    {
        $rows_array = AflReports::leftJoin('afl_clients', 'afl_reports.account_id', '=', 'afl_clients.client_id')
        ->where('afl_reports.account_id', 0)
        ->where('afl_reports.product_id', 0)
        ->orderBy('report_date_time', 'DESC')
        ->orderBy('report_id', 'DESC')->get()->toArray();
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }
            $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
            $item_array['report_date_time'] = removeSeconds($item_array['report_date_time']);
            $item_array['report_status_formatted'] = returnFormattedReportStatusArray($item_array['report_status']);

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    public function reportArrayLicense(Request $request)
    {
        $product_id = $request->get('product_id');
        $rows_array = AflReports::leftJoin('afl_clients', 'afl_reports.account_id', '=', 'afl_clients.client_id')
                        ->where('afl_reports.product_id', $product_id)
                        ->where('afl_reports.report_system', 0)
                        ->orderBy('report_date_time', 'DESC')
                        ->orderBy('report_id', 'DESC')->get()->toArray();
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
            $item_array['report_date_time'] = removeSeconds($item_array['report_date_time']);
            $item_array['report_status_formatted'] = returnFormattedReportStatusArray($item_array['report_status']);

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    public function reportArrayUpdate()
    {
        $rows_array = AflReports::leftJoin('afl_products', 'afl_reports.product_id', '=', 'afl_products.product_id')
                                 ->orderBy('report_date_time', 'DESC')
                                 ->orderBy('report_id', 'DESC')->get()->toArray();
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }
            if (empty($item_array['product_title'])) {
                $item_array['product_title'] = 'Unknown';
            }
            $item_array['report_date_time'] = removeSeconds($item_array['report_date_time']);
            $item_array['report_status_formatted'] = returnFormattedReportStatusArray($item_array['report_status']);
            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //format system report user
    private function formatSystemReportUser($admin_fname, $admin_lname, $admin_email)
    {
        if (filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            if (! empty($admin_fname) && ! empty($admin_lname)) {
                $user_formatted = "$admin_fname $admin_lname";
            } else {
                $user_formatted = $admin_email;
            }
        } else {
            $user_formatted = 'System';
        }

        return $user_formatted;
    }
}
