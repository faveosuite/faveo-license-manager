<?php

namespace App\Http\Controllers\Admin;

//namespace App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    public function search(Request $request)
    {
        $isLicenseSearchApi = (bool) $request->get('isLicenseSearchApi');

        $result = $this->searchByParams($request->all(), $this->ip_address);

        if (! $result['success']) {
            $errorMessages = [
                'invalid_api_key' => 'Invalid details has been looked for here',
                'invalid_search_type' => $isLicenseSearchApi
                    ? Lang::get('invalid_search_type')
                    : 'Invalid search type.<br>',
                'invalid_search_term' => $isLicenseSearchApi
                    ? Lang::get('invalid_search_term_min_3_characters')
                    : 'Invalid search term (3 characters minimum).<br>',
                'no_results' => $isLicenseSearchApi
                    ? 'There was an error searching for the particular detail in license manager'
                    : 'No results found.',
            ];

            $message = $errorMessages[$result['error']] ?? 'Unknown error';

            if ($isLicenseSearchApi && in_array($result['error'], ['invalid_search_type', 'invalid_search_term'])) {
                return errorResponse($message, 400);
            }

            $errorDetail = in_array($result['error'], ['invalid_search_type', 'invalid_search_term', 'no_results']) && ! $isLicenseSearchApi
                ? ['error' => "Search could not be performed because of this reason: <br><br>$message"]
                : $message;

            return json_encode([
                'api_action_success' => $result['error'] === 'invalid_api_key' ? 0 : 1,
                'api_error_detected' => $isLicenseSearchApi ? 1 : 0,
                'action_success' => 0,
                'error_detected' => 1,
                'page_message' => $errorDetail,
            ]);
        }

        return json_encode([
            'api_action_success' => 1,
            'api_error_detected' => 0,
            'action_success' => 1,
            'error_detected' => 0,
            'page_message' => $result['data'],
        ]);
    }

    /**
     * Core search logic — takes plain params, returns array (no HTTP response).
     * Can be called from streams or other non-HTTP contexts.
     *
     * @return array{success: bool, data: mixed, error: string|null}
     */
    public function searchByParams(array $params, ?string $ipAddress = null): array
    {
        $apiKeySecret = $params['api_key_secret'] ?? null;
        $searchType = $params['search_type'] ?? null;
        $searchKeyword = $params['search_keyword'] ?? '';
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $isLicenseSearchApi = (bool) ($params['isLicenseSearchApi'] ?? false);

        $supportedSearchTypes = ['banned_host', 'callback', 'client', 'installation', 'license', 'product', 'report', 'version'];

        // Load settings
        foreach (DB::table('afl_settings')->get()->toArray() as $row) {
            extract((array) $row);
        }

        if (empty($dateFrom) || ! aflVerifyDateTime($dateFrom, 'Y-m-d')) {
            $dateFrom = setDefaultDateFrom($RECORDS_ARCHIVE_DAYS ?? 365);
        }
        if (empty($dateTo) || ! aflVerifyDateTime($dateTo, 'Y-m-d')) {
            $dateTo = '';
        }

        // API key check — only when api_key_secret is provided
        if (! empty($apiKeySecret)) {
            $apiKey = new ApiKeysController();
            $apiActionSuccess = $apiKey->apiKeyCheck($apiKeySecret, $ipAddress ?? '');

            if ($apiActionSuccess != 1) {
                return ['success' => false, 'data' => null, 'error' => 'invalid_api_key'];
            }
        }

        if (! in_array($searchType, $supportedSearchTypes)) {
            return ['success' => false, 'data' => null, 'error' => 'invalid_search_type'];
        }

        if (mb_strlen(trim($searchKeyword), 'UTF-8') < 3) {
            return ['success' => false, 'data' => null, 'error' => 'invalid_search_term'];
        }

        $recordsLimit = $RECORDS_ON_SEARCH_PAGE ?? 50;

        if ($isLicenseSearchApi) {
            [$rowsArray, $elementsToUnset] = $this->fetchLicenseSearchResults($searchType, $dateFrom, $dateTo, $searchKeyword, $recordsLimit);
        } else {
            [$rowsArray, $elementsToUnset] = $this->fetchUpdateSearchResults($searchType, $dateFrom, $dateTo, $searchKeyword, $recordsLimit);
        }

        if (empty($rowsArray)) {
            return ['success' => false, 'data' => null, 'error' => 'no_results'];
        }

        $this->unsetArrayElements($rowsArray, $elementsToUnset);

        return ['success' => true, 'data' => $rowsArray, 'error' => null];
    }

    protected function fetchLicenseSearchResults(string $type, $dateFrom, $dateTo, $keyword, $limit): array
    {
        $unsetKeys = [
            'banned_host' => [],
            'callback' => ['product_title', 'product_description', 'product_sku', 'product_url_homepage', 'product_url_download', 'product_date', 'product_version', 'product_envato_id', 'product_status', 'client_fname', 'client_lname', 'client_email', 'client_active_date', 'client_cancel_date', 'client_status', 'client_formatted', 'callback_status_formatted'],
            'client' => ['client_status_formatted'],
            'installation' => ['product_title', 'product_description', 'product_sku', 'product_url_homepage', 'product_url_download', 'product_date', 'product_version', 'product_envato_id', 'product_status', 'client_fname', 'client_lname', 'client_email', 'client_active_date', 'client_cancel_date', 'client_status', 'client_formatted', 'installation_status_formatted'],
            'license' => ['product_title', 'product_description', 'product_sku', 'product_url_homepage', 'product_url_download', 'product_date', 'product_version', 'product_envato_id', 'product_status', 'client_fname', 'client_lname', 'client_email', 'client_active_date', 'client_cancel_date', 'client_status', 'client_formatted', 'license_status_formatted'],
            'product' => ['product_status_formatted'],
            'report' => ['account_id', 'client_fname', 'client_lname', 'client_email', 'client_active_date', 'client_cancel_date', 'client_status', 'client_formatted', 'report_status_formatted'],
        ];

        $rows = match ($type) {
            'banned_host' => $this->returnBannedHostsArray($dateFrom, $dateTo, $keyword, $limit),
            'callback' => $this->returnCallbacksArray(0, $dateFrom, $dateTo, $keyword, $limit),
            'client' => $this->returnClientsArray($keyword, $limit),
            'installation' => $this->returnInstallationsArray(0, $dateFrom, $dateTo, $keyword, $limit),
            'license' => $this->returnLicensesArray(0, $keyword, $limit),
            'product' => $this->returnProductsArray($keyword, $limit),
            'report' => $this->returnLicenseReportsArray(0, $dateFrom, $dateTo, $keyword, $limit),
            default => [],
        };

        return [$rows, $unsetKeys[$type] ?? []];
    }

    protected function fetchUpdateSearchResults(string $type, $dateFrom, $dateTo, $keyword, $limit): array
    {
        $unsetKeys = [
            'banned_host' => [],
            'callback' => ['product_title', 'product_sku', 'product_short_description', 'product_full_description', 'product_url_homepage', 'product_url_order', 'product_price', 'product_key', 'product_max_active_versions', 'product_date', 'product_status', 'version_install_file', 'version_install_query', 'version_raw_install_query', 'version_upgrade_file', 'version_upgrade_query', 'version_raw_upgrade_query', 'version_install_limit', 'version_install_count', 'version_upgrade_limit', 'version_upgrade_count', 'version_changelog', 'version_date', 'version_expire_date', 'version_comments', 'version_status', 'callback_status_formatted'],
            'installation' => ['product_title', 'product_sku', 'product_short_description', 'product_full_description', 'product_url_homepage', 'product_url_order', 'product_price', 'product_key', 'product_max_active_versions', 'product_date', 'product_status', 'version_number', 'version_install_file', 'version_install_query', 'version_raw_install_query', 'version_upgrade_file', 'version_upgrade_query', 'version_raw_upgrade_query', 'version_install_limit', 'version_install_count', 'version_upgrade_limit', 'version_upgrade_count', 'version_changelog', 'version_date', 'version_expire_date', 'version_comments', 'version_status', 'installation_status_formatted'],
            'product' => ['product_key', 'product_status_formatted'],
            'report' => ['account_id', 'report_status_formatted', 'product_title', 'product_sku', 'product_short_description', 'product_full_description', 'product_url_homepage', 'product_url_order', 'product_price', 'product_key', 'product_max_active_versions', 'product_date', 'product_status'],
            'version' => ['version_install_file', 'version_install_query', 'version_raw_install_query', 'version_upgrade_file', 'version_upgrade_query', 'version_raw_upgrade_query', 'product_key', 'product_max_active_versions', 'product_title', 'product_sku', 'product_short_description', 'product_full_description', 'product_url_homepage', 'product_url_order', 'product_price', 'product_date', 'product_status', 'total_callbacks', 'version_status_formatted'],
        ];

        $rows = match ($type) {
            'banned_host' => $this->returnBannedHostsArray($dateFrom, $dateTo, $keyword, $limit),
            'callback' => $this->returnUpdateCallbacksArray(0, $dateFrom, $dateTo, $keyword, $limit),
            'installation' => $this->returnUpdateInstallationsArray(0, $dateFrom, $dateTo, $keyword, $limit),
            'product' => $this->returnUpdateProductsArray($keyword, $limit),
            'report' => $this->returnUpdateReportsArray(0, $dateFrom, $dateTo, $keyword, $limit),
            'version' => $this->returnUpdateVersionsArray(0, $keyword, $limit),
            default => [],
        };

        return [$rows, $unsetKeys[$type] ?? []];
    }

    /* LICENSE MANAGER APIS */
    //return banned hosts
    public function returnBannedHostsArray($date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afl_banned_hosts')
                    ->where('banned_host_date', '>=', $date_from)
                    ->where('banned_host_date', '<=', $date_to)
                    ->orWhere('banned_host_ip', 'like', $search_keyword)
                    ->orWhere('banned_host_comments', 'like', $search_keyword)
                    ->orderBy('banned_host_date', 'desc')
                    ->orderBy('banned_host_id', 'desc')
                    ->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afl_banned_hosts')
                     ->where('banned_host_date', '>=', $date_from)
                     ->where('banned_host_date', '<=', $date_to)
                     ->orderBy('banned_host_date', 'desc')
                     ->orderBy('banned_host_id', 'desc')
                     ->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            if (! aflVerifyDateTime($item_array['banned_host_last_block_date'], 'Y-m-d')) {
                $item_array['banned_host_last_block_date'] = '';
            }

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return callbacks
    public function returnCallbacksArray($product_id, $date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];
        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afl_callbacks')
                     ->leftJoin('afl_products', 'afl_callbacks.product_id', '=', 'afl_products.product_id')
                     ->leftJoin('users', 'afl_callbacks.client_id', '=', 'users.client_id')
                     ->where('afl_callbacks.callback_date_time', '>=', $date_from)
                     ->where('afl_callbacks.callback_date_time', '<=', $date_to)
                     ->orWhere('users.client_email', 'like', $search_keyword)
                     ->orWhere('afl_callbacks.license_code', 'like', $search_keyword)
                     ->orWhere('afl_callbacks.callback_domain', 'like', $search_keyword)
                     ->orWhere('afl_callbacks.callback_ip', 'like', $search_keyword)
                     ->orderBy('afl_callbacks.callback_date_time', 'desc')
                     ->orderBy('afl_callbacks.callback_id', 'desc')
                     ->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afl_callbacks')
                   ->leftJoin('afl_products', 'afl_callbacks.product_id', '=', 'afl_products.product_id')
                   ->leftJoin('users', 'afl_callbacks.client_id', '=', 'users.client_id')
                   ->where('afl_callbacks.product_id', '=', $product_id)
                   ->where('afl_callbacks.callback_date_time', '>=', $date_from)
                   ->where('afl_callbacks.callback_date_time', '<=', $date_to)
                   ->orderBy('afl_callbacks.callback_date_time', 'desc')
                   ->orderBy('afl_callbacks.callback_id', 'desc')->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
            $item_array['callback_date_time'] = removeSeconds($item_array['callback_date_time']);
            $item_array['callback_status_formatted'] = returnFormattedStatusArray($item_array['callback_status'], 'Success', 'Error', 'Unknown');

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return clients
    public function returnClientsArray($search_keyword = '', $results_limit = 0)
    {
        $root_array = [];
        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('users')
                         ->select('users.*',
                             DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE users.client_id=afl_licenses.client_id) AS total_licenses'),
                             DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE users.client_id=afl_installations.client_id) AS total_installations'),

                         )->orWhere('client_fname', 'like', $search_keyword)
                                   ->orWhere('client_lname', 'like', $search_keyword)
                                   ->orWhere('client_email', 'like', $search_keyword)
                                   ->orderBy('client_fname')->orderBy('client_lname')->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('users')
                         ->select('users.*',
                             DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE users.client_id=afl_licenses.client_id) AS total_licenses'),
                             DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE users.client_id=afl_installations.client_id) AS total_installations'),

                         )->orWhere('client_fname', 'like', $search_keyword)
                                   ->orWhere('client_lname', 'like', $search_keyword)
                                   ->orWhere('client_email', 'like', $search_keyword)
                                   ->orderBy('client_fname')->orderBy('client_lname')->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['client_status_formatted'] = returnFormattedStatusArray($item_array['client_status']);

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return installations
    public function returnInstallationsArray($product_id, $date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];
        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afl_installations')
                    ->leftJoin('afl_products', 'afl_installations.product_id', '=', 'afl_products.product_id')
                    ->leftJoin('users', 'afl_installations.client_id', '=', 'users.client_id')
                    ->where('afl_installations.installation_date', '>=', $date_from)
                    ->where('afl_installations.installation_date', '<=', $date_to)
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('users.client_email', 'like', $search_keyword)
                            ->orWhere('afl_installations.license_code', 'like', $search_keyword)
                            ->orWhere('afl_installations.installation_domain', 'like', $search_keyword)
                            ->orWhere('afl_installations.installation_ip', 'like', $search_keyword);
                    })
                    ->orderBy('installation_date', 'desc')
                    ->orderBy('installation_id', 'desc')
                    ->get()->toArray(); //showing installation path details
        } else {
            $rows_array = DB::table('afl_installations')
                     ->leftJoin('afl_products', 'afl_installations.product_id', '=', 'afl_products.product_id')
                     ->leftJoin('users', 'afl_installations.client_id', '=', 'users.client_id')
                     ->where('afl_installations.product_id', '=', $product_id)
                     ->where('afl_installations.installation_date', '>=', $date_from)
                     ->where('afl_installations.installations_date', '<=', $date_to)
                     ->orderBy('installation_date', 'desc')
                     ->orderBy('insatalltion_id', 'desc')->get()->toArray();
        }

        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
            $item_array['installation_status_formatted'] = returnFormattedStatusArray($item_array['installation_status'], 'Active', 'Inactive', 'Unknown');

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return licenses
    public function returnLicensesArray($product_id, $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afl_licenses')
                    ->select('afl_licenses.*', 'users.client_email',
                        DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_licenses.product_id=afl_installations.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_installations.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_installations.license_code)) AS total_installations'),
                        DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_licenses.product_id=afl_callbacks.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_callbacks.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_callbacks.license_code) ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'))
                    ->join('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
                    ->leftJoin('users', 'afl_licenses.client_id', '=', 'users.client_id')
                    ->orWhere('afl_licenses.license_code', 'like', $search_keyword)
                    ->orWhere('users.client_email', 'like', $search_keyword)
                    ->orWhere('afl_licenses.license_comments', 'like', $search_keyword)
                    ->orderBy('license_date', 'desc')
                    ->orderBy('license_id', 'desc')
                    ->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afl_licenses')
                     ->select('afl_licenses.*', 'users.client_email',
                         DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_licenses.product_id=afl_installations.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_installations.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_installations.license_code)) AS total_installations'),
                         DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_licenses.product_id=afl_callbacks.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_callbacks.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_callbacks.license_code) ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time')
                     )->join('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
                        ->leftJoin('users', 'afl_licenses.client_id', '=', 'users.client_id')
                        ->where('afl_licenses.product_id', $product_id)
                        ->orderBy('license_date', 'desc')
                        ->orderBy('license_id', 'desc')
                        ->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            if (! aflValidateIntegerValue($item_array['license_limit'])) {
                $item_array['license_limit'] = '';
            }

            if (aflVerifyDateTime($item_array['license_expire_date'], 'Y-m-d') && $item_array['license_expire_date'] <= date('Y-m-d')) { //expired status will be formatted
                $item_array['license_status'] = 2;
            }

            if (! aflVerifyDateTime($item_array['license_expire_date'], 'Y-m-d')) {
                $item_array['license_expire_date'] = '';
            }

            if (! aflVerifyDateTime($item_array['license_updates_date'], 'Y-m-d')) {
                $item_array['license_updates_date'] = '';
            }

            if (! aflVerifyDateTime($item_array['license_support_date'], 'Y-m-d')) {
                $item_array['license_support_date'] = '';
            }

            //$item_array['client_email']= null;
            $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
            $item_array['latest_callback_date_time'] = removeSeconds($item_array['latest_callback_date_time']);
            $item_array['license_status_formatted'] = returnFormattedStatusArray($item_array['license_status'], 'Active', 'Inactive', 'Expired');
            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return products
    public function returnProductsArray($search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afl_products')
                       ->select('afl_products.*',
                           DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id) AS total_licenses'),
                           DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id) AS total_installations'),
                           DB::raw('(SELECT COUNT(*) FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id) AS total_callbacks'),
                           DB::raw('(SELECT COUNT(*) FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id) AS total_reports'),
                           DB::raw('(SELECT license_date FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id ORDER BY afl_licenses.license_date DESC, afl_licenses.license_id DESC LIMIT 1) AS latest_license_date'),
                           DB::raw('(SELECT installation_date FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id ORDER BY afl_installations.installation_date DESC, afl_installations.installation_id DESC LIMIT 1) AS latest_installation_date'),
                           DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                           DB::raw('(SELECT report_date_time FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1) AS latest_report_date_time'),

                       )->orWhere('product_title', 'like', $search_keyword)->orWhere('product_sku', 'like', $search_keyword)

                                ->orderBy('product_title')->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afl_products')
                       ->select('afl_products.*',
                           DB::raw('(SELECT COUNT(*) FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id) AS total_licenses'),
                           DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id) AS total_installations'),
                           DB::raw('(SELECT COUNT(*) FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id) AS total_callbacks'),
                           DB::raw('(SELECT COUNT(*) FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id) AS total_reports'),
                           DB::raw('(SELECT license_date FROM afl_licenses WHERE afl_products.product_id=afl_licenses.product_id ORDER BY afl_licenses.license_date DESC, afl_licenses.license_id DESC LIMIT 1) AS latest_license_date'),
                           DB::raw('(SELECT installation_date FROM afl_installations WHERE afl_products.product_id=afl_installations.product_id ORDER BY afl_installations.installation_date DESC, afl_installations.installation_id DESC LIMIT 1) AS latest_installation_date'),
                           DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_products.product_id=afl_callbacks.product_id ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                           DB::raw('(SELECT report_date_time FROM afl_reports WHERE afl_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1) AS latest_report_date_time'),

                       )->orWhere('product_title', $search_keyword)->orWhere('product_sku', $search_keyword)

                                ->orderBy('product_title')->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['latest_callback_date_time'] = removeSeconds($item_array['latest_callback_date_time']);
            $item_array['latest_report_date_time'] = removeSeconds($item_array['latest_report_date_time']);
            $item_array['product_status_formatted'] = returnFormattedStatusArray($item_array['product_status']);
            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return license reports
    public function returnLicenseReportsArray($product_id, $date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards
            $rows_array = DB::table('afl_reports')
                     ->leftJoin('users', 'afl_reports.account_id', '=', 'users.client_id')
                     ->orWhere('afl_reports.report_text', 'like', $search_keyword)
                     ->orWhere('afl_reports.license_code', 'like', $search_keyword)
                     ->orWhere('users.client_email', 'like', $search_keyword)
                     ->where('afl_reports.report_system', '=', 0)
                     ->where('afl_reports.report_date_time', '>=', $date_from)
                     ->where('afl_reports.report_date_time', '<=', $date_to)
                     ->orderBy('report_date_time', 'desc')
                     ->orderBy('report_id', 'desc')
                     ->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afl_reports')
                   ->leftJoin('users', ' afl_reports.account_id', '=', 'users.client_id')
                   ->where('afl_reports.product_id', '=', $product_id)
                   ->where('afl_reports.report_system', '=', 0)
                   ->where('afl_reports.report_date_time', '>=', $date_from)
                   ->where('afl_reports.report_date_time', '<=', $date_to)
                   ->orderBy('report_date_time', 'desc')
                   ->orderBy('report_id', 'desc')->get()->toArray();
        }
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

    /* UPDATE MANAGER APIS */

    //return callbacks
    public function returnUpdateCallbacksArray($product_id, $date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afu_callbacks')
                ->leftJoin('afu_products', 'afu_callbacks.product_id', '=', 'afu_products.product_id')
                ->leftJoin('afu_versions', 'afu_callbacks.version_id', '=', 'afu_versions.version_id')
                ->where('afu_callbacks.callback_date_time', '>=', $date_from)
                ->where('afu_callbacks.callback_date_time', '<=', $date_to)
                ->orWhere('afu_callbacks.callback_ip', 'like', $search_keyword)
                ->orderBy('afu_callbacks.callback_date_time', 'desc')
                ->orderBy('afu_callbacks.callback_id', 'desc')
                ->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afu_callbacks')
                ->leftJoin('afu_products', 'afu_callbacks.product_id', '=', 'afu_products.product_id')
                ->leftJoin('afu_versions', 'afu_callbacks.version_id', '=', 'afu_versions.version_id')
                ->where('afu_callbacks.product_id', '=', $product_id)
                ->where('afu_callbacks.callback_date_time', '>=', $date_from)
                ->where('afu_callbacks.callback_date_time', '<=', $date_to)
                ->orderBy('afu_callbacks.callback_date_time', 'desc')
                ->orderBy('afu_callbacks.callback_id', 'desc')->get()->toArray();
        }
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

    //return installations
    public function returnUpdateInstallationsArray($product_id, $date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afu_installations')
                ->leftJoin('afu_products', 'afu_installations.product_id', '=', 'afu_products.product_id')
                ->leftJoin('afu_versions', 'afu_installations.version_id', '=', 'afu_versions.version_id')
                ->where('afu_installations.installation_date', '>=', $date_from)
                ->where('afu_installations.installation_date', '<=', $date_to)
                ->orWhere('afu_products.product_title', 'like', $search_keyword)
                ->orWhere('afu_installations.installation_ip', 'like', $search_keyword)
                ->orderBy('installation_date', 'desc')
                ->orderBy('installation_id', 'desc')
                ->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afu_installations')
                ->leftJoin('afu_products', 'afu_installations.product_id', '=', 'afu_products.product_id')
                ->leftJoin('afu_versions', 'afu_installations.version_id', '=', 'afu_versions.version_id')
                ->where('afu_installations.product_id', '=', $product_id)
                ->where('afu_installations.installation_date', '>=', $date_from)
                ->where('afu_installations.installation_date', '<=', $date_to)
                ->orderBy('installation_date', 'desc')
                ->orderBy('insatalltion_id', 'desc')->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            $item_array['installation_status_formatted'] = returnFormattedStatusArray($item_array['installation_status'], 'Active', 'Inactive', 'Unknown');

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return products
    public function returnUpdateProductsArray($search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afu_products')
                ->select('afu_products.*',
                    DB::raw('(SELECT COUNT(*) FROM afu_versions WHERE afu_products.product_id=afu_versions.product_id) AS total_versions'),
                    DB::raw('(SELECT COUNT(*) FROM afu_installations WHERE afu_products.product_id=afu_installations.product_id) AS total_installations'),
                    DB::raw('(SELECT COUNT(*) FROM afu_callbacks WHERE afu_products.product_id=afu_callbacks.product_id) AS total_callbacks'),
                    DB::raw('(SELECT COUNT(*) FROM afl_reports WHERE afu_products.product_id=afl_reports.product_id) AS total_reports'),
                    DB::raw('(SELECT version_number FROM afu_versions WHERE afu_products.product_id=afu_versions.product_id ORDER BY afu_versions.version_date DESC, afu_versions.version_id DESC LIMIT 1) AS latest_version_number'),
                    DB::raw('(SELECT version_date FROM afu_versions WHERE afu_products.product_id=afu_versions.product_id ORDER BY afu_versions.version_date DESC, afu_versions.version_id DESC LIMIT 1) AS latest_version_date'),
                    DB::raw('(SELECT installation_date FROM afu_installations WHERE afu_products.product_id=afu_installations.product_id ORDER BY afu_installations.installation_date DESC, afu_installations.installation_id DESC LIMIT 1) AS latest_installation_date'),
                    DB::raw('(SELECT callback_date_time FROM afu_callbacks WHERE afu_products.product_id=afu_callbacks.product_id ORDER BY afu_callbacks.callback_date_time DESC, afu_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                    DB::raw('(SELECT report_date_time FROM afl_reports WHERE afu_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1) AS latest_report_date_time'),

                )->orWhere('product_title', 'like', $search_keyword)->orWhere('product_sku', 'like', $search_keyword)

                ->orderBy('product_title')->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afu_products')
                ->select('afu_products.*',
                    DB::raw('(SELECT COUNT(*) FROM afu_versions WHERE afu_products.product_id=afu_versions.product_id) AS total_versions'),
                    DB::raw('(SELECT COUNT(*) FROM afu_installations WHERE afu_products.product_id=afu_installations.product_id) AS total_installations'),
                    DB::raw('(SELECT COUNT(*) FROM afu_callbacks WHERE afu_products.product_id=afu_callbacks.product_id) AS total_callbacks'),
                    DB::raw('(SELECT COUNT(*) FROM afl_reports WHERE afu_products.product_id=afl_reports.product_id) AS total_reports'),
                    DB::raw('(SELECT version_number FROM afu_versions WHERE afu_products.product_id=afu_versions.product_id ORDER BY afu_versions.version_date DESC, afu_versions.version_id DESC LIMIT 1) AS latest_version_number'),
                    DB::raw('(SELECT version_date FROM afu_versions WHERE afu_products.product_id=afu_versions.product_id ORDER BY afu_versions.version_date DESC, afu_versions.version_id DESC LIMIT 1) AS latest_version_date'),
                    DB::raw('(SELECT installation_date FROM afu_installations WHERE afu_products.product_id=afu_installations.product_id ORDER BY afu_installations.installation_date DESC, afu_installations.installation_id DESC LIMIT 1) AS latest_installation_date'),
                    DB::raw('(SELECT callback_date_time FROM afu_callbacks WHERE afu_products.product_id=afu_callbacks.product_id ORDER BY afu_callbacks.callback_date_time DESC, afu_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                    DB::raw('(SELECT report_date_time FROM afl_reports WHERE afu_products.product_id=afl_reports.product_id ORDER BY afl_reports.report_date_time DESC, afl_reports.report_id DESC LIMIT 1) AS latest_report_date_time'),

                )->orderBy('product_title')->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            if (! filter_var($item_array['product_price'], FILTER_VALIDATE_FLOAT)) {
                $item_array['product_price'] = '';
            }

            $item_array['latest_callback_date_time'] = removeSeconds($item_array['latest_callback_date_time']);
            $item_array['latest_report_date_time'] = removeSeconds($item_array['latest_report_date_time']);
            $item_array['product_status_formatted'] = returnFormattedStatusArray($item_array['product_status']);

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return update reports
    public function returnUpdateReportsArray($product_id, $date_from = '', $date_to = '', $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];

        if (! aflVerifyDateTime($date_from, 'Y-m-d')) {
            $date_from = '0000-00-00';
        }

        if (! aflVerifyDateTime($date_to, 'Y-m-d')) {
            $date_to = '9999-12-31';
        }

        $date_to .= ' 23:59:59'; //include all records of last specified day

        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards
            $rows_array = DB::table('afl_reports')

                         ->leftJoin('afu_products', 'afl_reports.product_id', '=', 'afu_products.product_id')
                         ->where('afl_reports.report_text', 'like', $search_keyword)
                         //->where('afl_reports.report_system',0)
                         ->where('afl_reports.report_date_time', '>=', $date_from)
                         ->where('afl_reports.report_date_time', '<=', $date_to)
                         ->orderBy('report_date_time', 'desc')
                         ->limit($results_limit)
                         ->get()->toArray();
        } else {
            $rows_array = DB::table('afl_reports')
                ->leftJoin('afu_products', 'afl_reports.product_id', '=', 'afu_products.product_id')
                ->where('afl_reports.product_id', $product_id)
                ->where('afl_reports.report_system', 0)
                ->where('afl_reports.report_date_time', '>=', $date_from)
                ->where('afl_reports.report_date_time', '<=', $date_to)
                ->orderBy('report_date_time', 'desc')
                ->orderBy('report_id', 'desc')
                ->get()->toArray();
        }
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

    //return versions
    public function returnUpdateVersionsArray($product_id, $search_keyword = '', $results_limit = 0)
    {
        $root_array = [];
        if (! empty($search_keyword) && aflValidateIntegerValue($results_limit)) {
            $search_keyword = "%$search_keyword%"; //add wildcards

            $rows_array = DB::table('afu_versions')
                           ->select('afu_versions.*', DB::raw('(SELECT COUNT(*) FROM afu_callbacks WHERE afu_versions.version_id=afu_callbacks.version_id) AS total_callbacks'))
                           ->join('afu_products', 'afu_versions.product_id', '=', 'afu_products.product_id')
                           ->orWhere('afu_products.product_title', 'like', $search_keyword)
                           ->orWhere('afu_products.product_sku', 'like', $search_keyword)
                           ->orWhere('afu_versions.version_number', 'like', $search_keyword)
                           ->orWhere('afu_versions.version_comments', 'like', $search_keyword)
                           ->orderBy('afu_versions.version_date', 'desc')
                           ->orderBy('afu_versions.version_id', 'desc')->limit($results_limit)->get()->toArray();
        } else {
            $rows_array = DB::table('afu_versions')
                ->select(DB::raw('(SELECT COUNT(*) FROM afu_callbacks WHERE afu_versions.version_id=afu_callbacks.version_id) AS total_callbacks'))
                ->Join('afu_products', 'afu_versions.product_id', '=', 'afu_products.product_id')
                ->where('afu_versions.product_id', $product_id)
                ->orderBy('afu_versions.version_date', 'desc')
                ->orderBy('afu_versions.version_id', 'desc')->get()->toArray();
        }
        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }

            if (aflVerifyDateTime($item_array['version_expire_date'], 'Y-m-d') && $item_array['version_expire_date'] <= date('Y-m-d')) { //expired status will be formatted
                $item_array['version_status'] = 2;
            }

            if (! aflVerifyDateTime($item_array['version_expire_date'], 'Y-m-d')) {
                $item_array['version_expire_date'] = '';
            }

            $item_array['version_status_formatted'] = returnFormattedStatusArray($item_array['version_status'], 'Active', 'Inactive', 'Expired');

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

    //remove elements with specified keys from standard/multi-dimensional array. function modifies variable directly (doesn't return any data).
    public function unsetArrayElements($array, $keys_to_unset_array)
    {
        foreach ($array as $key => $value) {
            if (in_array($key, $keys_to_unset_array, true)) { //element needs to be removed from array (use true for strict comparison, otherwise 0th element will be removed from sub-array)
                unset($array[$key]);
            } else {
                if (is_array($value)) { //it's a multi-dimensional array, re-apply function to each sub-array
                    $this->unsetArrayElements($value, $keys_to_unset_array);
                }
            }
        }
    }
}
