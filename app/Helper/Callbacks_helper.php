<?php

use App\Models\AflFailedLicensings;
use App\Models\AflNotifications;
use App\Models\AflReports;
use Illuminate\Support\Facades\DB;

//return root url from long url (http://www.domain.com/path/file.php?aa=xx becomes http://www.domain.com/path/), remove scheme, www. and last slash if needed
function getRootUrl($url, $remove_scheme, $remove_www, $remove_path, $remove_last_slash)
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $url_array = parse_url($url); //parse URL into arrays like $url_array['scheme'], $url_array['host'], etc

        $url = str_ireplace($url_array['scheme'].'://', '', $url); //make URL without scheme, so no :// is included when searching for first or last /

        if ($remove_path == 1) { //remove everything after FIRST / in URL, so it becomes "real" root URL
            $first_slash_position = stripos($url, '/'); //find FIRST slash - the end of root URL
            if ($first_slash_position > 0) { //cut URL up to FIRST slash
                $url = substr($url, 0, $first_slash_position + 1);
            }
        } else { //remove everything after LAST / in URL, so it becomes "normal" root URL
            $last_slash_position = strripos($url, '/'); //find LAST slash - the end of root URL
            if ($last_slash_position > 0) { //cut URL up to LAST slash
                $url = substr($url, 0, $last_slash_position + 1);
            }
        }

        if ($remove_scheme != 1) { //scheme was already removed, add it again
            $url = $url_array['scheme'].'://'.$url;
        }

        if ($remove_www == 1) { //remove www.
            $url = str_ireplace('www.', '', $url);
        }

        if ($remove_last_slash == 1) { //remove / from the end of URL if it exists
            while (substr($url, -1) == '/') { //use cycle in case URL already contained multiple // at the end
                $url = substr($url, 0, -1);
            }
        }
    }

    return trim($url);
}

//set variable value only if it has no value yet
function setValue($old_value, $new_value)
{
    $value_to_return = '';

    if (empty($old_value)) {
        $value_to_return = $new_value;
    } else {
        $value_to_return = $old_value;
    }

    return $value_to_return;
}

    //check if request to licensing server is valid (contains a valid code or email)
function isValidLicenseRequest($license_code, $client_email)
{
    $result = false;

    if (! empty($license_code) || filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
        $result = true;
    }

    return $result;
}

//verify signature received from user's script
function verifyScriptSignature($license_signature, $product_id, $root_url, $client_email, $license_code)
{
    global $ROOT_URL;
    $result = false;
    $ROOT_URL = url('/');
    $root_ips_array = gethostbynamel(aflGetRawDomain($ROOT_URL));
    /*if(is_null($client_email)){
        $client_email="";
    }*/
    if (! empty($root_ips_array) && ! empty($license_signature)) {
        if (hash('sha256', gmdate('Y-m-d').$root_url.$client_email.$license_code.$product_id.implode('', $root_ips_array)) == $license_signature) {
            $result = true;
        }
    }

    return $result;
}

//return server notification with case, properly formatted text, signature, and additional data (if any) by adding this data right into server headers
function returnServerNotification($notification_case, $root_url, $ip_address, $client_email, $client_fname, $client_lname, $license_code, $product_id, $product_title, $product_description, $product_url_homepage, $product_url_download, $product_version, $license_expire_date, $license_cancel_date, $license_updates_date, $license_support_date, $license_limit, $notification_data = '')
{
    $content_array = [];
    $notification_server_signature = generateServerSignature($product_id, $root_url, $client_email, $license_code);

    $rows_array = AflNotifications::where('notification_id', 1)->get()->toArray(); //fetchRow("SELECT * FROM apl_notifications WHERE notification_id=?", array(1), array("i"));

    foreach ($rows_array as $row) {
        extract((array) $row);
    }
    $notification_text = DB::table('afl_notifications')->where('notification_id', 1)->value($notification_case);
    $bad_text_array = ['%ROOT_URL%', '%IP_ADDRESS%', '%CLIENT_EMAIL%', '%CLIENT_FNAME%', '%CLIENT_LNAME%', '%LICENSE_CODE%', '%PRODUCT_ID%', '%PRODUCT_TITLE%', '%PRODUCT_DESCRIPTION%', '%PRODUCT_URL_HOMEPAGE%', '%PRODUCT_URL_DOWNLOAD%', '%PRODUCT_VERSION%', '%LICENSE_EXPIRE_DATE%', '%LICENSE_CANCEL_DATE%', '%LICENSE_UPDATES_DATE%', '%LICENSE_SUPPORT_DATE%', '%LICENSE_LIMIT%'];
    $good_text_array = [$root_url, $ip_address, $client_email, $client_fname, $client_lname, $license_code, $product_id, $product_title, $product_description, $product_url_homepage, $product_url_download, $product_version, $license_expire_date, $license_cancel_date, $license_updates_date, $license_support_date, $license_limit];
    $notification_text = str_ireplace($bad_text_array, $good_text_array, $notification_text);

    if ($notification_case != 'notification_license_ok') { //only return additional data if everything OK, otherwise unset it
        $notification_data = '';
    }

    return response()->json([])
           ->header('notification_case', $notification_case)
           ->header('notification_text', $notification_text)
           ->header('notification_server_signature', $notification_server_signature)
           ->header('notification_data', json_encode($notification_data));
}

//create license report
function createLicenseReport($SMART_REPORTS, $product_id, $account_id, $license_code, $report_text, $report_status)
{
    $date_today = date('Y-m-d');
    $report_date_time = date('Y-m-d H:i:s');
    $report_system = 0; //license reports should never be system

    if ($SMART_REPORTS == 1) { //check if such report already exists today
        $rows_array = AflReports::where('product_id', $product_id)
                      ->where(function ($query) use ($account_id, $license_code) {
                          $query->where('account_id', $account_id)
                                ->orWhere('license_code', $license_code);
                      })->whereRaw('report_date_time BETWEEN ? AND ?', ["$date_today 00:00:00", "$date_today 23:59:59"])
                      ->where('report_text', $report_text)
                      ->where('report_system', $report_system)
                      ->get()->toArray();
        //fetchRow("SELECT * FROM apl_reports WHERE product_id=? AND (account_id=? OR license_code=?) AND report_date_time BETWEEN ? AND ? AND report_text=? AND report_system=?", array($product_id, $account_id, $license_code, "$date_today 00:00:00", "$date_today 23:59:59", $report_text, $report_system), array("i", "i", "s", "s", "s", "s", "i"));
    }

    if (empty($rows_array)) { //no identical report found (or SMART_REPORTS disabled)
        DB::table('afl_reports')->insertOrIgnore([
            'product_id' => $product_id,
            'account_id' => $account_id,
            'license_code' => $license_code,
            'report_date_time' => $report_date_time,
            'report_text' => $report_text,
            'report_system' => $report_system,
            'report_status' => $report_status,
        ]); //doMysqlQuery("INSERT IGNORE INTO apl_reports (product_id, account_id, license_code, report_date_time, report_text, report_system, report_status) VALUES (?, ?, ?, ?, ?, ?, ?)", array($product_id, $account_id, $license_code, $report_date_time, $report_text, $report_system, $report_status), array("i", "i", "s", "s", "s", "i", "i"));
    }
}

//record failed licensing attempt and ban host if needed
function recordFailedLicensing($BANNED_HOSTS, $FAILED_LICENSINGS_LIMIT, $ip_address)
{
    if ($BANNED_HOSTS == 1 && $FAILED_LICENSINGS_LIMIT != 0 && filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $failed_licensing_last_attempt_date = date('Y-m-d');

        if (! empty($rows_array = AflFailedLicensings::where('failed_licensing_ip', $ip_address)
                                                  ->get()->toArray())) {//fetchRow("SELECT * FROM apl_failed_licensings WHERE failed_licensing_ip=?", array($ip_address), array("s")))) //failed licensing from specified IP recorded already, update existing record
            foreach ($rows_array as $row) {
                extract((array) $row);
            }

            $failed_licensing_attempts++;

            DB::table('afl_failed_licensings')->where('failed_licensing_id', $failed_licensing_id)
               ->update(['failed_licensing_attempts' => $failed_licensing_attempts,
                   'failed_licensing_last_attempt_date' => $failed_licensing_last_attempt_date,
               ]);
        //doMysqlQuery("UPDATE apl_failed_licensings SET failed_licensing_attempts=?, failed_licensing_last_attempt_date=? WHERE failed_licensing_id=?", array($failed_licensing_attempts, $failed_licensing_last_attempt_date, $failed_licensing_id), array("i", "s", "i"));
        } else { //failed licensing from specified IP not recorded yet, add new record
            $failed_licensing_attempts = 1;

            DB::table('afl_failed_licensings')->insertOrIgnore([
                'failed_licensing_ip' => $ip_address,
                'failed_licensing_attempts' => $failed_licensing_attempts,
                'failed_licensing_last_attempt_date' => $failed_licensing_last_attempt_date,
            ]); //doMysqlQuery("INSERT IGNORE INTO apl_failed_licensings (failed_licensing_ip, failed_licensing_attempts, failed_licensing_last_attempt_date) VALUES (?, ?, ?)", array($ip_address, $failed_licensing_attempts, $failed_licensing_last_attempt_date), array("s", "i", "s"));
        }

        if ($failed_licensing_attempts >= $FAILED_LICENSINGS_LIMIT) { //failed licensing attempts limit reached, ban host
            $banned_host_comments = "Auto-ban: maximum failed licensing attempts ($FAILED_LICENSINGS_LIMIT) reached.";

            DB::table('afl_banned_hosts')
              ->insertOrIgnore([
                  'banned_host_ip' => $ip_address,
                  'banned_host_comments' => $banned_host_comments,
                  'banned_host_date' => $banned_host_date,
              ]);
            //doMysqlQuery("INSERT IGNORE INTO apl_banned_hosts (banned_host_ip, banned_host_comments, banned_host_date) VALUES (?, ?, ?)", array($ip_address, $banned_host_comments, $failed_licensing_last_attempt_date), array("s", "s", "s"));
            createReport("Host $ip_address auto-banned (maximum failed licensing attempts ($FAILED_LICENSINGS_LIMIT) reached).", 0, 1, 2);
        }
    }
}

//generate signature to be sent to user's script
function generateServerSignature($product_id, $root_url, $client_email, $license_code)
{
    global $ROOT_URL;
    $ROOT_URL = url('/');
    $license_signature = '';
    $root_ips_array = gethostbynamel(aflGetRawDomain($ROOT_URL));

    if (! empty($root_ips_array)) { //IP(s) resolved successfully
        $license_signature = hash('sha256', implode('', $root_ips_array).$product_id.$license_code.$client_email.$root_url.gmdate('Y-m-d'));
    }

    return $license_signature;
}

    function productArray()
    {
        $root_array = [];
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

                                )->orderBy('product_title')->get()->toArray();

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

    function licenseArray()
    {
        $root_array = [];
        $rows_array = DB::table('afl_licenses')
      ->select('afl_licenses.*', 'afl_clients.client_email', 'afl_products.product_title',
                DB::raw('(SELECT COUNT(*) FROM afl_installations WHERE afl_licenses.product_id=afl_installations.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_installations.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_installations.license_code)) AS total_installations'),
                DB::raw('(SELECT callback_date_time FROM afl_callbacks WHERE afl_licenses.product_id=afl_callbacks.product_id AND (afl_licenses.client_id IS NOT NULL AND afl_licenses.client_id=afl_callbacks.client_id OR afl_licenses.client_id IS NULL AND afl_licenses.license_code IS NOT NULL AND afl_licenses.license_code=afl_callbacks.license_code) ORDER BY afl_callbacks.callback_date_time DESC, afl_callbacks.callback_id DESC LIMIT 1) AS latest_callback_date_time'),
                DB::raw('(SELECT COUNT(*) FROM afl_callbacks WHERE afl_callbacks.license_code = afl_licenses.license_code) AS total_callbacks')
           )
        ->leftJoin('afl_products', 'afl_licenses.product_id', '=', 'afl_products.product_id')
        ->leftJoin('afl_clients', 'afl_licenses.client_id', '=', 'afl_clients.client_id')
        ->orderBy('license_date', 'desc')
        ->orderBy('license_id', 'desc')->get()->toArray();

        foreach ($rows_array as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }
            $total_license = DB::table('afl_licenses')->where('license_code', $item_array['license_code'])->get('license_code')->count();
            $item_array['total_license'] = $total_license;
            $latest_license = DB::table('afl_licenses')->where('license_code', $item_array['license_code'])->orderBy('license_date', 'desc')->get('license_date');
            $item_array['latest_license'] = $latest_license[0];
            $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
            $item_array['license_status_formatted'] = returnFormattedStatusArray($item_array['license_status'], 'Active', 'Inactive', 'Unknown');

            $root_array[] = $item_array;
        }

        return $root_array;
    }

function installArray()
{
    $root_array = [];
    $rows_array = DB::table('afl_installations')
        ->leftJoin('afl_products', 'afl_installations.product_id', '=', 'afl_products.product_id')
        ->leftJoin('afl_clients', 'afl_installations.client_id', '=', 'afl_clients.client_id')
        ->orderBy('installation_date', 'desc')
        ->orderBy('installation_id', 'desc')->get()->toArray();

    foreach ($rows_array as $row) {
        foreach ($row as $key => $value) {
            $item_array[$key] = $value;
        }
        $total_installations = DB::table('afl_installations')->where('license_code', $item_array['license_code'])->get('license_code')->count();
        $item_array['total_installations'] = $total_installations;
        $latest_installation = DB::table('afl_installations')->where('license_code', $item_array['license_code'])->orderBy('installation_date', 'desc')->get('installation_date');
        $item_array['latest_installation'] = $latest_installation[0];
        $item_array['client_formatted'] = formatClient($item_array['license_code'], $item_array['client_email']);
        $item_array['installation_status_formatted'] = returnFormattedStatusArray($item_array['installation_status'], 'Active', 'Inactive', 'Unknown');

        $root_array[] = $item_array;
    }

    return $root_array;
}

//create product callback and update installations/upgrades count if installation/upgrade was performed successfully
function createProductCallback($SMART_REPORTS, $product_id, $version_id, $callback_ip, $callback_path, $callback_type, $callback_status, $version_install_count, $version_upgrade_count)
{
    $date_today = date('Y-m-d');
    $callback_date_time = date('Y-m-d H:i:s');

    if ($SMART_REPORTS == 1) { //check if such callback already exists today
        $rows_array = DB::table('afu_callbacks')
                     ->where('product_id', $product_id)
                     ->where('version_id', $version_id)
                     ->where('callback_ip', $callback_ip)
                     ->where('callback_path', $callback_path)
                     ->where('callback_type', $callback_type)
                     ->whereRaw('callback_date_time BETWEEN ? AND ?', ["$date_today 00:00:00", "$date_today 23:59:59"])
                     ->where('callback_status', $callback_status)->get()->toArray(); //fetchRow("SELECT * FROM aus_callbacks WHERE product_id=? AND version_id=? AND callback_ip=? AND callback_path=? AND callback_type=? AND callback_date_time BETWEEN ? AND ? AND callback_status=?", array($product_id, $version_id, $callback_ip, $callback_path, $callback_type, "$date_today 00:00:00", "$date_today 23:59:59", $callback_status), array("i", "i", "s", "s", "i", "s", "s", "i"));
    }

    if (empty($rows_array)) { //no excessive callbacks found (or SMART_REPORTS disabled)
        DB::table('afu_callbacks')->insertOrIgnore([
            'product_id' => $product_id,
            'version_id' => $version_id,
            'callback_ip' => $callback_ip,
            'callback_path' => $callback_path,
            'callback_type' => $callback_type,
            'callback_date_time' => $callback_date_time,
            'callback_status' => $callback_status,
        ]); //doMysqlQuery("INSERT IGNORE INTO aus_callbacks (product_id, version_id, callback_ip, callback_path, callback_type, callback_date_time, callback_status) VALUES (?, ?, ?, ?, ?, ?, ?)", array($product_id, $version_id, $callback_ip, $callback_path, $callback_type, $callback_date_time, $callback_status), array("i", "i", "s", "s", "i", "s", "i"));
    }

    if ($callback_status == 1) { //update installations/upgrades count ($version_install_count and $version_upgrade_count values were already increased by 1 before calling this function
        \App\Models\AfuVersions::where('version_id', $version_id)
            ->update([
                'version_install_count' => $version_install_count,
                'version_upgrade_count' => $version_upgrade_count,
            ]);
    }
}

//create product report
function createProductReport($SMART_REPORTS, $product_id, $report_text, $report_status)
{
    $date_today = date('Y-m-d');
    $report_date_time = date('Y-m-d H:i:s');
    $report_system = 0; //product reports should never be system

    if ($SMART_REPORTS == 1) { //check if such report already exists today
        $rows_array = AflReports::where('product_id', $product_id)
                        ->whereRaw('report_date_time BETWEEN ? AND ?', ["$date_today 00:00:00", "$date_today 23:59:59"])
                        ->where('report_text', $report_text)
                        ->where('report_system', $report_system)->get()->toArray(); //fetchRow("SELECT * FROM aus_reports WHERE product_id=? AND report_date_time BETWEEN ? AND ? AND report_text=? AND report_system=?", array($product_id, "$date_today 00:00:00", "$date_today 23:59:59", $report_text, $report_system), array("i", "s", "s", "s", "i"));
    }

    if (empty($rows_array)) { //no excessive reports found (or SMART_REPORTS disabled)
        DB::table('afl_reports')->insertOrIgnore([
            'product_id' => $product_id,
            'report_date_time' => $report_date_time,
            'report_text' => $report_text,
            'report_system' => $report_system,
            'report_status' => $report_status,
        ]); //doMysqlQuery("INSERT IGNORE INTO aus_reports (product_id, report_date_time, report_text, report_system, report_status) VALUES (?, ?, ?, ?, ?)", array($product_id, $report_date_time, $report_text, $report_system, $report_status), array("i", "s", "s", "i", "i"));
    }
}

//record failed update attempt and ban host if needed
function recordFailedUpdate($BANNED_HOSTS, $FAILED_UPDATES_LIMIT, $ip_address)
{
    if ($BANNED_HOSTS == 1 && $FAILED_UPDATES_LIMIT != 0 && filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $failed_update_last_attempt_date = date('Y-m-d');

        if (! empty($rows_array = DB::table('afu_failed_updates')->where('failed_update_ip', $ip_address)->get()->toArray())) {//fetchRow("SELECT * FROM aus_failed_updates WHERE failed_update_ip=?", array($ip_address), array("s")))) //failed update from specified IP recorded already, update existing record
            foreach ($rows_array as $row) {
                extract((array) $row);
            }

            $failed_update_attempts++;

            DB::table('afu_failed_updates')->where('failed_update_id', $failed_update_id)->update([
                'failed_update_attempts' => $failed_update_attempts,
                'failed_update_last_attempt_date' => $failed_update_last_attempt_date,
            ]); //doMysqlQuery("UPDATE aus_failed_updates SET failed_update_attempts=?, failed_update_last_attempt_date=? WHERE failed_update_id=?", array($failed_update_attempts, $failed_update_last_attempt_date, $failed_update_id), array("i", "s", "i"));
        } else { //failed update from specified IP not recorded yet, add new record
            $failed_update_attempts = 1;

            DB::table('afu_failed_updates')->insertOrIgnore([
                'failed_update_ip' => $ip_address,
                'failed_update_attempts' => $failed_update_attempts,
                'failed_update_last_attempt_date' => $failed_update_last_attempt_date,
            ]); //doMysqlQuery("INSERT IGNORE INTO aus_failed_updates (failed_update_ip, failed_update_attempts, failed_update_last_attempt_date) VALUES (?, ?, ?)", array($ip_address, $failed_update_attempts, $failed_update_last_attempt_date), array("s", "i", "s"));
        }

        if ($failed_update_attempts >= $FAILED_UPDATES_LIMIT) { //failed update attempts limit reached, ban host
            $banned_host_comments = "Auto-ban: maximum failed update attempts ($FAILED_UPDATES_LIMIT) reached.";

            DB::table('afl_banned_hosts')->insertOrIgnore([
                'banned_host_ip' => $ip_address,
                'banned_host_comments' => $banned_host_comments,
                'banned_host_date' => $failed_update_last_attempt_date,
            ]); //doMysqlQuery("INSERT IGNORE INTO aus_banned_hosts (banned_host_ip, banned_host_comments, banned_host_date) VALUES (?, ?, ?)", array($ip_address, $banned_host_comments, $failed_update_last_attempt_date), array("s", "s", "s"));
            createReport("Host $ip_address auto-banned (maximum failed update attempts ($FAILED_UPDATES_LIMIT) reached).", 0, 1, 2);
        }
    }
}

//verify script signature received from user's script
function afuVerifyScriptSignature($ROOT_URL, $script_signature, $product_id, $product_key)
{
    $result = false;
    $ROOT_URL = url('/');
    $root_ips_array = gethostbynamel(aflGetRawDomain($ROOT_URL));

    //dd(hash("sha256", gmdate("Y-m-d").$product_id.$product_key.implode("", $root_ips_array)));
    if (! empty($script_signature) && ! empty($root_ips_array)) {
        if (hash('sha256', gmdate('Y-m-d').$product_id.$product_key.implode('', $root_ips_array)) == $script_signature) {
            $result = true;
        }
    }

    return $result;
}

//return server notification with case, properly formatted text, signature, and additional data (if any) by adding this data right into server headers
function returnUpdateServerNotification($ROOT_URL, $notification_case, $product_id, $product_title, $product_key, $product_short_description, $product_full_description, $product_url_homepage, $product_url_order, $version_number, $version_expire_date, $version_install_limit, $version_upgrade_limit, $ip_address, $notification_data = '')
{
    $notification_server_signature = generateUpdateServerSignature($ROOT_URL, $product_id, $product_key);

    $rows_array = DB::table('afu_notifications')->where('notification_id', 1)->get()->toArray(); //fetchRow("SELECT * FROM aus_notifications WHERE notification_id=?", array(1), array("i"));
    foreach ($rows_array as $row) {
        extract((array) $row);
    }

    $bad_text_array = ['%PRODUCT_ID%', '%PRODUCT_TITLE%', '%PRODUCT_SHORT_DESCRIPTION%', '%PRODUCT_FULL_DESCRIPTION%', '%PRODUCT_URL_HOMEPAGE%', '%PRODUCT_URL_ORDER%', '%VERSION_NUMBER%', '%VERSION_EXPIRE_DATE%', '%VERSION_INSTALL_LIMIT%', '%VERSION_UPGRADE_LIMIT%', '%IP_ADDRESS%'];
    $good_text_array = [$product_id, $product_title, $product_short_description, $product_full_description, $product_url_homepage, $product_url_order, $version_number, $version_expire_date, $version_install_limit, $version_upgrade_limit, $ip_address];
    $notification_text = str_ireplace($bad_text_array, $good_text_array, $$notification_case);
    if ($notification_case != 'notification_operation_ok') { //only return additional data if everything OK, otherwise unset it
        $notification_data = '';
    }

    header("notification_case: $notification_case");
    header("notification_text: $notification_text");
    header("notification_server_signature: $notification_server_signature");
    header('notification_data: '.json_encode($notification_data));
}

//generate server signature to be sent to user's script
function generateUpdateServerSignature($ROOT_URL, $product_id, $product_key)
{
    $server_signature = '';
    $ROOT_URL = url('/');
    $root_ips_array = gethostbynamel(aflGetRawDomain($ROOT_URL));

    if (! empty($root_ips_array)) { //IP(s) resolved successfully
        $server_signature = hash('sha256', implode('', $root_ips_array).$product_key.$product_id.gmdate('Y-m-d'));
    }

    return $server_signature;
}

function callbackArray()
{
    $rows_array = DB::table('afl_callbacks')
        ->leftJoin('afl_products', 'afl_callbacks.product_id', '=', 'afl_products.product_id')
        ->leftJoin('afl_clients', 'afl_callbacks.client_id', '=', 'afl_clients.client_id')
        ->orderBy('afl_callbacks.callback_date_time', 'desc')
        ->orderBy('afl_callbacks.callback_id', 'desc')->get()->toArray();
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
function upgradeScriptSignature($product_id,$product_key){
    $ROOT_URL = url('/');
    $root_ips_array = gethostbynamel(aflGetRawDomain($ROOT_URL));
    return hash('sha256', gmdate('Y-m-d').$product_id.$product_key.implode('', $root_ips_array));
}
