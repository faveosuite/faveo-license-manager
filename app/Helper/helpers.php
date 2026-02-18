<?php

use App\Models\AflSettings;
use App\Http\Controllers\PhpMailController;
use App\Models\User;
use App\Models\AflClients;
use App\Models\AflApiKeys;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\File;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
//check Auto Faveo Licenser core configuration and return an array with error messages if something wrong
function aflCheckSettings()
{
    $notifications_array = [];

    if (empty(config('constants.Basic.AFL_SALT')) || config('constants.Basic.AFL_SALT') == 'some_random_text') { //invalid encryption salt
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_SALT');
    }

    if (! filter_var(config('constants.Basic.AFL_ROOT_URL'), FILTER_VALIDATE_URL) || ! ctype_alnum(substr(config('constants.Basic.AFL_ROOT_URL'), -1))) { //invalid Auto Faveo Licenser server URL
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_ROOT_URL');
    }

    if (! aflValidateIntegerValue(config('constants.Basic.AFL_PRODUCT_ID'))) { //invalid product ID
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_PRODUCT_ID');
    }

    if (! aflValidateIntegerValue(config('constants.Basic.AFL_DAYS'), 1, 365)) { //invalid verification period
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_VERIFICATION_PERIOD');
    }

    if (config('constants.Basic.AFL_STORAGE') != 'DATABASE' && config('constants.Basic.AFL_STORAGE') != 'FILE') { //invalid license storage
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_STORAGE');
    }

    if (config('constants.Basic.AFL_STORAGE') == 'DATABASE' && ! ctype_alnum(str_ireplace(['_'], '', config('constants.Basic.AFL_DATABASE_TABLE')))) { //invalid license table name
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_TABLE');
    }

    if (config('constants.Basic.AFL_STORAGE') == 'FILE' && ! @is_writable(config('constants.Extra.AFL_DIRECTORY').'/'.config('constants.Basic.AFL_LICENSE_FILE_LOCATION'))) { //invalid license file or permissions
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_LICENSE_FILE');
    }

    if (! empty(config('constants.Advanced.AFL_ROOT_IP')) && ! filter_var(config('constants.Advanced.AFL_ROOT_IP'), FILTER_VALIDATE_IP)) { //invalid Auto PHP Licenser server IP
        $notifications_array[] = \config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_ROOT_IP');
    }

    if (! empty(config('constants.Advanced.AFL_ROOT_IP')) && ! in_array(config('constants.Advanced.AFL_ROOT_IP'), gethostbynamel(aflGetRawDomain(config('constants.Basic.AFL_ROOT_URL'))))) { //actual IP address of Auto PHP Licenser server doesn't match specified IP address
        //dd(gethostbynamel(aflGetRawDomain(\config('constants.Basic.AFL_ROOT_URL'))));
        $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_DNS');
    }

    if (defined('APL_ROOT_NAMESERVERS') && ! empty(config('constants.Advanced.AFL_ROOT_NAMESERVERS'))) { //check if nameservers are valid (use "defined" to check if nameservers are set because APL_ROOT_NAMESERVERS is commented by default to prevent errors in PHP<7)
        foreach (config('constants.Advanced.AFL_ROOT_NAMESERVERS') as $nameserver) {
            if (! aflValidateRawDomain($nameserver)) { //invalid Auto PHP Licenser server nameservers
                $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_ROOT_NAMESERVERS');
                break;
            }
        }
    }

    if (defined('APL_ROOT_NAMESERVERS') && ! empty(config('constants.Advanced.AFL_ROOT_NAMESERVERS'))) { //check if actual nameservers of Auto PHP Licenser server domain match specified nameservers (use "defined" to check if nameservers are set because APL_ROOT_NAMESERVERS is commented by default to prevent errors in PHP<7)
        $apl_root_nameservers_array = config('constants.Advanced.AFL_ROOT_NAMESERVERS'); //create a variable from constant in order to use sort and other array functions
        $fetched_nameservers_array = [];

        $dns_records_array = dns_get_record(aflGetRawDomain(config('constants.Basic.AFL_ROOT_URL')), DNS_NS);
        foreach ($dns_records_array as $record) {
            $fetched_nameservers_array[] = $record['target'];
        }

        $apl_root_nameservers_array = array_map('strtolower', $apl_root_nameservers_array); //convert root nameservers to lowercase
        $fetched_nameservers_array = array_map('strtolower', $fetched_nameservers_array); //convert fetched nameservers to lowercase

        sort($apl_root_nameservers_array); //sort both arrays before comparison
        sort($fetched_nameservers_array);
        if ($apl_root_nameservers_array != $fetched_nameservers_array) {
            $notifications_array[] = config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_DNS'); //actual nameservers of Auto PHP Licenser server don't match specified nameservers
        }
    }

    return $notifications_array;
}

//make post requests with cookies and referrers, return array with server headers, errors, and body content
function aflCustomPost($url, $post_info = '', $refer = '')
{
    $user_agent = 'phpmillion cURL';
    $connect_timeout = 10;
    $server_response_array = [];
    $formatted_headers_array = [];

    if (filter_var($url, FILTER_VALIDATE_URL) && ! empty($post_info)) {
        if (empty($refer) || ! filter_var($refer, FILTER_VALIDATE_URL)) { //use original URL as refer when no valid refer URL provided
            $refer = $url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_REFERER, $refer);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_info);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //this function is called by curl for each header received - https://stackoverflow.com/questions/9183178/can-php-curl-retrieve-response-headers-and-body-in-a-single-request
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use ($formatted_headers_array) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) { //ignore invalid headers
                    return $len;
                }

                $name = strtolower(trim($header[0]));
                $formatted_headers_array[$name] = trim($header[1]);

                return $len;
            }
        );

        $result = curl_exec($ch);
        $curl_error = curl_error($ch); //returns a human readable error (if any)
        curl_close($ch);
        $server_response_array['headers'] = $formatted_headers_array;
        $server_response_array['error'] = $curl_error;
        $server_response_array['body'] = $result;
    }
    //dd($server_response_array);

    return $server_response_array;
}

//process response from Auto PHP Licenser server. if response received, validate it and parse notifications and data (if any). if response not received or is invalid, return a corresponding notification
function aflParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
{
    $notifications_array = [];
    if (! empty($content_array)) { //response received, validate it
        if (! empty($content_array['headers']['notification_server_signature']) && aflVerifyServerSignature($content_array['headers']['notification_server_signature'], $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)) { //response valid
            $notifications_array['notification_case'] = $content_array['headers']['notification_case'];
            $notifications_array['notification_text'] = $content_array['headers']['notification_text'];
            if (! empty($content_array['headers']['notification_data'])) { //additional data returned
                $notifications_array['notification_data'] = json_decode($content_array['headers']['notification_data'], true);
            }
        } else { //response invalid
            $notifications_array['notification_case'] = 'notification_invalid_response';
            $notifications_array['notification_text'] = config('constants.Basic.AFL_NOTIFICATION_INVALID_RESPONSE');
        }
    } else { //no response received
        $notifications_array['notification_case'] = 'notification_no_connection';
        $notifications_array['notification_text'] = config('constants.Basic.AFL_NOTIFICATION_NO_CONNECTION');
    }

    return $notifications_array;
}

//verify signature received from Auto PHP Licenser server
function aflVerifyServerSignature($notification_server_signature, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
{
    $result = false;
    $root_ips_array = gethostbynamel(aflGetRawDomain(config('constants.Basic.AFL_ROOT_URL')));

    if (! empty($notification_server_signature) && ! empty($ROOT_URL) && isset($CLIENT_EMAIL) && isset($LICENSE_CODE) && ! empty($root_ips_array)) {
        if (hash('sha256', implode('', $root_ips_array).config('constants.Basic.AFL_PRODUCT_ID').$LICENSE_CODE.$CLIENT_EMAIL.$ROOT_URL.gmdate('Y-m-d')) == $notification_server_signature) {
            $result = true;
        }
    }

    return $result;
}

//encrypt text with custom key
function aflCustomEncrypt($string, $key)
{
    $encrypted_string = '';

    if (! empty($string) && ! empty($key)) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')); //generate an initialization vector

        $encrypted_string = openssl_encrypt($string, 'aes-256-cbc', $key, 0, $iv); //encrypt the string using AES 256 encryption in CBC mode using encryption key and initialization vector
        $encrypted_string = base64_encode($encrypted_string.'::'.$iv); //the $iv is just as important as the key for decrypting, so save it with encrypted string using a unique separator "::"
    }

    return $encrypted_string;
}

//validate integer and check if it's between min and max values
use Illuminate\Support\Facades\DB;

function aflValidateIntegerValue($number, $min_value = 1, $max_value = 999999999)
{
    $result = false;

    if (! is_float($number) && filter_var($number, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min_value, 'max_range' => $max_value]]) !== false) { //don't allow numbers like 1.0 to bypass validation
        $result = true;
    }

    return $result;
}

//get raw domain (returns (sub.)domain.com from url like http://www.(sub.)domain.com/something.php?xx=yy)
function aflGetRawDomain($url)
{
    $raw_domain = '';

    if (! empty($url)) {
        $scheme = parse_url($url, PHP_URL_SCHEME); //check if scheme exists because URL can't be parsed properly without a scheme
        if (empty($scheme)) { //add a temporary http:// scheme before parsing if needed
            $url = 'http://'.$url;
        }

        $raw_domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
    }

    return $raw_domain;
}

//validate raw domain (only URL like (sub.)domain.com will validate)
function aflValidateRawDomain($url)
{
    $result = false;

    if (! empty($url)) {
        if (preg_match('/^[a-z0-9-.]+\.[a-z\.]{2,7}$/', strtolower($url))) { //check if this is valid tld
            $result = true;
        }
    }

    return $result;
}

function errorResponse($message, $statusCode = 500)
{
    /**
     * When developers simply want to send info of exceptions they are handling
     * using errorResponse() and explicitly pass $e->getCode() as second argument
     * then if thrown exception has code as 0 then response() will throw an error
     * and response will be modified to new exception. We are handling this here
     * so that developers can easily use this method and simply pass $e->getCode()
     * to use for response HTTP code.
     */
    $statusCode = ($statusCode) ?: 500;

    return response()->json(['success' => false, 'message' => $message], $statusCode);
}

/**
 * Format success message/data into json success response
 *
 * @param  string  $message Success message
 * @param  array|string  $data Data of the response
 * @param  int  $statusCode
 * @return \Illuminate\Http\JsonResponse json response
 */
function successResponse($message = '', $data = '', $statusCode = 200)
{
    $response = ['success' => true];

    // if message given
    if (! empty($message)) {
        $response['message'] = $message;
    }

    // If data given
    // if (!empty($data)) {
    $response['data'] = $data;
    // }

    return response()->json($response, $statusCode);
}

/**
 * verify date and/or time according to provided format (such as Y-m-d, Y-m-d H:i, H:i, and so on)
 *
 * @param $datetime
 * @param $format
 * @return $result
 */
function aflVerifyDateTime($datetime, $format)
{
    $result = false;

    if (! empty($datetime) && ! empty($format)) {
        $datetime = DateTime::createFromFormat($format, $datetime);
        $errors = DateTime::getLastErrors();

        if ($datetime && empty($errors['warning_count'])) { //datetime OK
            $result = true;
        }
    }

    return $result;
}

//create report
function createReport($report_text, $account_id, $report_system, $report_status)
{
    if (! empty($report_text) && aflValidateIntegerValue($report_system, 0, 1)) {
        $report_date_time = date('Y-m-d H:i:s');

        // doMysqlQuery("INSERT IGNORE INTO apl_reports (account_id, report_date_time, report_text, report_system, report_status) VALUES (?, ?, ?, ?, ?)", array($account_id, $report_date_time, $report_text, $report_system, $report_status), array("i", "s", "s", "i", "i"));
        try {
            DB::table('afl_reports')->insertOrIgnore([
                'account_id' => $account_id,
                'report_date_time' => $report_date_time,
                'report_text' => $report_text,
                'report_system' => $report_system,
                'report_status' => $report_status,
            ]);

            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }
}

//set default start date
function setDefaultDateFrom($RECORDS_HIDE_DAYS)
{
    if (aflValidateIntegerValue($RECORDS_HIDE_DAYS)) { //hide records older than RECORDS_HIDE_DAYS days
        $date_from = date('Y-m-d', strtotime("-$RECORDS_HIDE_DAYS days"));
    } else { //set start date empty (all records will be included)
        $date_from = '';
    }

    return $date_from;
}

//format client
function formatClient($license_code, $client_email)
{
    if (! empty($license_code)) {
        $client_formatted = $license_code;
    } else {
        if (filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
            $client_formatted = $client_email;
        } else {
            $client_formatted = 'Unknown Client';
        }
    }

    return $client_formatted;
}

//remove seconds from (date)timestamp
function removeSeconds($timestamp)
{
    if (! empty($timestamp) && (aflVerifyDateTime($timestamp, 'Y-m-d H:i:s') || aflVerifyDateTime($timestamp, 'H:i:s'))) {
        $timestamp = substr($timestamp, 0, -3);
    }

    return $timestamp;
}
//format and return array with item status class and text
function returnFormattedStatusArray($status, $active_text = 'Active', $inactive_text = 'Inactive', $other_text = 'Other', $unknown_text = 'Unknown', $null_text = '')
{
    if (is_null($status)) { //don't apply formatting at all
        $item_array['status_class'] = '';
        $item_array['status_text'] = $null_text;
    } elseif (isZero($status)) { //inactive
        $item_array['status_class'] = 'text-red';
        $item_array['status_text'] = $inactive_text;
    } elseif ($status == 1) { //active
        $item_array['status_class'] = 'text-green';
        $item_array['status_text'] = $active_text;
    } elseif ($status == 2) { //other
        $item_array['status_class'] = 'text-yellow';
        $item_array['status_text'] = $other_text;
    } else { //unknown
        $item_array['status_class'] = 'text-blue';
        $item_array['status_text'] = $unknown_text;
    }

    return $item_array;
}

//format array with report status text and class
function returnFormattedReportStatusArray($status, $success_text = 'Success', $error_text = 'Error', $warning_text = 'Warning', $unknown_text = 'Unknown')
{
    if (isZero($status)) { //error
        $item_array['status_class'] = 'text-red';
        $item_array['status_text'] = $error_text;
    } elseif ($status == 1) { //success
        $item_array['status_class'] = 'text-green';
        $item_array['status_text'] = $success_text;
    } elseif ($status == 2) { //warning
        $item_array['status_class'] = 'text-yellow';
        $item_array['status_text'] = $warning_text;
    } else { //unknown
        $item_array['status_class'] = 'text-blue';
        $item_array['status_text'] = $unknown_text;
    }

    return $item_array;
}

//check if argument is equal to zero (only returns true if argument is 0 or "0", returns false when argument is empty or null)
function isZero($argument)
{
    $result = false;

    if (strlen($argument) == 1 && filter_var($argument, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 0]]) !== false) {
        $result = true;
    }

    return $result;
}

//generate secure random string
function generateRandomString($string_length = 0)
{
    if (! aflValidateIntegerValue($string_length)) {
        $string_length = mt_rand(16, 64);
    }

    $random_string = substr(bin2hex(openssl_random_pseudo_bytes($string_length)), 0, $string_length); //bin2hex makes string twice longer, truncate it to specified length

    return $random_string;
}

//parse license file and make an array with license data
function aflParseLicenseFile()
{
    $license_data_array = [];

    if (@is_readable(config('constants.Extra.AFL_DIRECTORY').'/'.config('constants.Basic.AFL_LICENSE_FILE_LOCATION'))) {
        $file_content = file_get_contents(config('constants.Extra.AFL_DIRECTORY').'/'.config('constants.Basic.AFL_LICENSE_FILE_LOCATION'));
        preg_match_all("/<([A-Z_]+)>(.*?)<\/([A-Z_]+)>/", $file_content, $matches, PREG_SET_ORDER);
        if (! empty($matches)) {
            foreach ($matches as $value) {
                if (! empty($value[1]) && $value[1] == $value[3]) {
                    $license_data_array[$value[1]] = $value[2];
                }
            }
        }
    }

    return $license_data_array;
}

/** generate signature to be submitted to Auto Faveo Licenser server
 *
 * @param $ROOT_URL
 * @param $CLIENT_EMAIL
 * @param $LICENSE_CODE
 * @return Hashed $script_signature
 */
function aflGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
{
    $script_signature = '';
    $root_ips_array = gethostbynamel(aflGetRawDomain(config('constants.Basic.AFL_ROOT_URL')));

    if (! empty($ROOT_URL) && isset($CLIENT_EMAIL) && isset($LICENSE_CODE) && ! empty($root_ips_array)) {
        $script_signature = Hash::make(gmdate('Y-m-d').$ROOT_URL.$CLIENT_EMAIL.$LICENSE_CODE.config('constants.Basic.AFL_PRODUCT_ID').implode('', $root_ips_array));
    }

    return $script_signature;
}

//format and return nice numbers dropdown array
function returnNumbersDropdownArray($numbers_array, $title, $disabled_title, $selected_value)
{
    $root_array = [];

    if (is_array($numbers_array) && ! empty($title) && ! empty($disabled_title)) {
        foreach ($numbers_array as $key => $value) {
            $item_array['value'] = $value;
            $item_array['title'] = $value; //use $value as option title by default

            if (isZero($item_array['value']) && ! empty($disabled_title)) { //format disabled title when value is 0
                $item_array['title'] = $disabled_title;
            }

            if ($item_array['value'] > 0 && ! empty($title)) { //format title by adding a specific word to it
                $item_array['title'] .= " $title";

                if ($item_array['value'] == 1 && substr($item_array['title'], -1) == 's') { //remove last "s" if needed, so "1 records" becomes "1 record"
                    $item_array['title'] = substr($item_array['title'], 0, -1);
                }
            }

            $item_array['selected'] = returnOptionStatus($item_array['value'], $selected_value);

            $root_array[] = $item_array;
        }
    }

    return $root_array;
}

//return selection status for dropdown option
function returnOptionStatus($current_value, $selected_values_array, $readonly = 0)
{
    $status = '';

    if (in_array(strval($current_value), array_map('strval', convertVariableToArray($selected_values_array)))) { //convert ID(s) to array, so function works with single-select and multi-select dropdowns. also compare both values as strings to avoid false positive when current_value is some string and selected_values_array contains 0
        $status = ' selected';
    } else {
        if ($readonly == 1) { //mark non-selected option as disabled (read-only)
            $status = ' disabled';
        }
    }

    return $status;
}

//convert variables into arrays
function convertVariableToArray($var_name)
{
    if (! is_array($var_name)) {
        $var_name = [$var_name];
    }

    return $var_name;
}
//return time zones
function returnTimezonesDropdownArray($TIMEZONE)
{
    $root_array = [];

    $timezones_array = ['Africa/Abidjan', 'Africa/Accra', 'Africa/Addis_Ababa', 'Africa/Algiers', 'Africa/Asmara', 'Africa/Bamako', 'Africa/Bangui', 'Africa/Banjul', 'Africa/Bissau', 'Africa/Blantyre', 'Africa/Brazzaville', 'Africa/Bujumbura', 'Africa/Cairo', 'Africa/Casablanca', 'Africa/Ceuta', 'Africa/Conakry', 'Africa/Dakar', 'Africa/Dar_es_Salaam', 'Africa/Djibouti', 'Africa/Douala', 'Africa/El_Aaiun', 'Africa/Freetown', 'Africa/Gaborone', 'Africa/Harare', 'Africa/Johannesburg', 'Africa/Juba', 'Africa/Kampala', 'Africa/Khartoum', 'Africa/Kigali', 'Africa/Kinshasa', 'Africa/Lagos', 'Africa/Libreville', 'Africa/Lome', 'Africa/Luanda', 'Africa/Lubumbashi', 'Africa/Lusaka', 'Africa/Malabo', 'Africa/Maputo', 'Africa/Maseru', 'Africa/Mbabane', 'Africa/Mogadishu', 'Africa/Monrovia', 'Africa/Nairobi', 'Africa/Ndjamena', 'Africa/Niamey', 'Africa/Nouakchott', 'Africa/Ouagadougou', 'Africa/Porto-Novo', 'Africa/Sao_Tome', 'Africa/Tripoli', 'Africa/Tunis', 'Africa/Windhoek', 'America/Adak', 'America/Anchorage', 'America/Anguilla', 'America/Antigua', 'America/Araguaina', 'America/Argentina/Buenos_Aires', 'America/Argentina/Catamarca', 'America/Argentina/Cordoba', 'America/Argentina/Jujuy', 'America/Argentina/La_Rioja', 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos', 'America/Argentina/Salta', 'America/Argentina/San_Juan', 'America/Argentina/San_Luis', 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia', 'America/Aruba', 'America/Asuncion', 'America/Atikokan', 'America/Bahia', 'America/Bahia_Banderas', 'America/Barbados', 'America/Belem', 'America/Belize', 'America/Blanc-Sablon', 'America/Boa_Vista', 'America/Bogota', 'America/Boise', 'America/Cambridge_Bay', 'America/Campo_Grande', 'America/Cancun', 'America/Caracas', 'America/Cayenne', 'America/Cayman', 'America/Chicago', 'America/Chihuahua', 'America/Costa_Rica', 'America/Creston', 'America/Cuiaba', 'America/Curacao', 'America/Danmarkshavn', 'America/Dawson', 'America/Dawson_Creek', 'America/Denver', 'America/Detroit', 'America/Dominica', 'America/Edmonton', 'America/Eirunepe', 'America/El_Salvador', 'America/Fortaleza', 'America/Glace_Bay', 'America/Godthab', 'America/Goose_Bay', 'America/Grand_Turk', 'America/Grenada', 'America/Guadeloupe', 'America/Guatemala', 'America/Guayaquil', 'America/Guyana', 'America/Halifax', 'America/Havana', 'America/Hermosillo', 'America/Indiana/Indianapolis', 'America/Indiana/Knox', 'America/Indiana/Marengo', 'America/Indiana/Petersburg', 'America/Indiana/Tell_City', 'America/Indiana/Vevay', 'America/Indiana/Vincennes', 'America/Indiana/Winamac', 'America/Inuvik', 'America/Iqaluit', 'America/Jamaica', 'America/Juneau', 'America/Kentucky/Louisville', 'America/Kentucky/Monticello', 'America/Kralendijk', 'America/La_Paz', 'America/Lima', 'America/Los_Angeles', 'America/Lower_Princes', 'America/Maceio', 'America/Managua', 'America/Manaus', 'America/Marigot', 'America/Martinique', 'America/Matamoros', 'America/Mazatlan', 'America/Menominee', 'America/Merida', 'America/Metlakatla', 'America/Mexico_City', 'America/Miquelon', 'America/Moncton', 'America/Monterrey', 'America/Montevideo', 'America/Montserrat', 'America/Nassau', 'America/New_York', 'America/Nipigon', 'America/Nome', 'America/Noronha', 'America/North_Dakota/Beulah', 'America/North_Dakota/Center', 'America/North_Dakota/New_Salem', 'America/Ojinaga', 'America/Panama', 'America/Pangnirtung', 'America/Paramaribo', 'America/Phoenix', 'America/Port-au-Prince', 'America/Port_of_Spain', 'America/Porto_Velho', 'America/Puerto_Rico', 'America/Rainy_River', 'America/Rankin_Inlet', 'America/Recife', 'America/Regina', 'America/Resolute', 'America/Rio_Branco', 'America/Santa_Isabel', 'America/Santarem', 'America/Santiago', 'America/Santo_Domingo', 'America/Sao_Paulo', 'America/Scoresbysund', 'America/Sitka', 'America/St_Barthelemy', 'America/St_Johns', 'America/St_Kitts', 'America/St_Lucia', 'America/St_Thomas', 'America/St_Vincent', 'America/Swift_Current', 'America/Tegucigalpa', 'America/Thule', 'America/Thunder_Bay', 'America/Tijuana', 'America/Toronto', 'America/Tortola', 'America/Vancouver', 'America/Whitehorse', 'America/Winnipeg', 'America/Yakutat', 'America/Yellowknife', 'Antarctica/Casey', 'Antarctica/Davis', 'Antarctica/DumontDUrville', 'Antarctica/Macquarie', 'Antarctica/Mawson', 'Antarctica/McMurdo', 'Antarctica/Palmer', 'Antarctica/Rothera', 'Antarctica/Syowa', 'Antarctica/Troll', 'Antarctica/Vostok', 'Arctic/Longyearbyen', 'Asia/Aden', 'Asia/Almaty', 'Asia/Amman', 'Asia/Anadyr', 'Asia/Aqtau', 'Asia/Aqtobe', 'Asia/Ashgabat', 'Asia/Baghdad', 'Asia/Bahrain', 'Asia/Baku', 'Asia/Bangkok', 'Asia/Beirut', 'Asia/Bishkek', 'Asia/Brunei', 'Asia/Chita', 'Asia/Choibalsan', 'Asia/Colombo', 'Asia/Damascus', 'Asia/Dhaka', 'Asia/Dili', 'Asia/Dubai', 'Asia/Dushanbe', 'Asia/Gaza', 'Asia/Hebron', 'Asia/Ho_Chi_Minh', 'Asia/Hong_Kong', 'Asia/Hovd', 'Asia/Irkutsk', 'Asia/Jakarta', 'Asia/Jayapura', 'Asia/Jerusalem', 'Asia/Kabul', 'Asia/Kamchatka', 'Asia/Karachi', 'Asia/Kathmandu', 'Asia/Khandyga', 'Asia/Kolkata', 'Asia/Krasnoyarsk', 'Asia/Kuala_Lumpur', 'Asia/Kuching', 'Asia/Kuwait', 'Asia/Macau', 'Asia/Magadan', 'Asia/Makassar', 'Asia/Manila', 'Asia/Muscat', 'Asia/Nicosia', 'Asia/Novokuznetsk', 'Asia/Novosibirsk', 'Asia/Omsk', 'Asia/Oral', 'Asia/Phnom_Penh', 'Asia/Pontianak', 'Asia/Pyongyang', 'Asia/Qatar', 'Asia/Qyzylorda', 'Asia/Rangoon', 'Asia/Riyadh', 'Asia/Sakhalin', 'Asia/Samarkand', 'Asia/Seoul', 'Asia/Shanghai', 'Asia/Singapore', 'Asia/Srednekolymsk', 'Asia/Taipei', 'Asia/Tashkent', 'Asia/Tbilisi', 'Asia/Tehran', 'Asia/Thimphu', 'Asia/Tokyo', 'Asia/Ulaanbaatar', 'Asia/Urumqi', 'Asia/Ust-Nera', 'Asia/Vientiane', 'Asia/Vladivostok', 'Asia/Yakutsk', 'Asia/Yekaterinburg', 'Asia/Yerevan', 'Atlantic/Azores', 'Atlantic/Bermuda', 'Atlantic/Canary', 'Atlantic/Cape_Verde', 'Atlantic/Faroe', 'Atlantic/Madeira', 'Atlantic/Reykjavik', 'Atlantic/South_Georgia', 'Atlantic/St_Helena', 'Atlantic/Stanley', 'Australia/Adelaide', 'Australia/Brisbane', 'Australia/Broken_Hill', 'Australia/Currie', 'Australia/Darwin', 'Australia/Eucla', 'Australia/Hobart', 'Australia/Lindeman', 'Australia/Lord_Howe', 'Australia/Melbourne', 'Australia/Perth', 'Australia/Sydney', 'Europe/Amsterdam', 'Europe/Andorra', 'Europe/Athens', 'Europe/Belgrade', 'Europe/Berlin', 'Europe/Bratislava', 'Europe/Brussels', 'Europe/Bucharest', 'Europe/Budapest', 'Europe/Busingen', 'Europe/Chisinau', 'Europe/Copenhagen', 'Europe/Dublin', 'Europe/Gibraltar', 'Europe/Guernsey', 'Europe/Helsinki', 'Europe/Isle_of_Man', 'Europe/Istanbul', 'Europe/Jersey', 'Europe/Kaliningrad', 'Europe/Kiev', 'Europe/Lisbon', 'Europe/Ljubljana', 'Europe/London', 'Europe/Luxembourg', 'Europe/Madrid', 'Europe/Malta', 'Europe/Mariehamn', 'Europe/Minsk', 'Europe/Monaco', 'Europe/Moscow', 'Europe/Oslo', 'Europe/Paris', 'Europe/Podgorica', 'Europe/Prague', 'Europe/Riga', 'Europe/Rome', 'Europe/Samara', 'Europe/San_Marino', 'Europe/Sarajevo', 'Europe/Simferopol', 'Europe/Skopje', 'Europe/Sofia', 'Europe/Stockholm', 'Europe/Tallinn', 'Europe/Tirane', 'Europe/Uzhgorod', 'Europe/Vaduz', 'Europe/Vatican', 'Europe/Vienna', 'Europe/Vilnius', 'Europe/Volgograd', 'Europe/Warsaw', 'Europe/Zagreb', 'Europe/Zaporozhye', 'Europe/Zurich', 'Indian/Antananarivo', 'Indian/Chagos', 'Indian/Christmas', 'Indian/Cocos', 'Indian/Comoro', 'Indian/Kerguelen', 'Indian/Mahe', 'Indian/Maldives', 'Indian/Mauritius', 'Indian/Mayotte', 'Indian/Reunion', 'Pacific/Apia', 'Pacific/Auckland', 'Pacific/Bougainville', 'Pacific/Chatham', 'Pacific/Chuuk', 'Pacific/Easter', 'Pacific/Efate', 'Pacific/Enderbury', 'Pacific/Fakaofo', 'Pacific/Fiji', 'Pacific/Funafuti', 'Pacific/Galapagos', 'Pacific/Gambier', 'Pacific/Guadalcanal', 'Pacific/Guam', 'Pacific/Honolulu', 'Pacific/Johnston', 'Pacific/Kiritimati', 'Pacific/Kosrae', 'Pacific/Kwajalein', 'Pacific/Majuro', 'Pacific/Marquesas', 'Pacific/Midway', 'Pacific/Nauru', 'Pacific/Niue', 'Pacific/Norfolk', 'Pacific/Noumea', 'Pacific/Pago_Pago', 'Pacific/Palau', 'Pacific/Pitcairn', 'Pacific/Pohnpei', 'Pacific/Port_Moresby', 'Pacific/Rarotonga', 'Pacific/Saipan', 'Pacific/Tahiti', 'Pacific/Tarawa', 'Pacific/Tongatapu', 'Pacific/Wake', 'Pacific/Wallis', 'UTC'];

    foreach ($timezones_array as $key => $value) {
        $item_array['value'] = $value;
        $item_array['title'] = $value;
        $item_array['selected'] = returnOptionStatus($item_array['value'], $TIMEZONE);

        $root_array[] = $item_array;
    }

    return $root_array;
}

//validate file (check if file exists, file mime and extension meet requirements, file doesn't exceed specified size). $file_name is needed to properly validate extension during upload because temp file is stored without extension
function validateFile($file_path_with_name, $file_name, $allowed_mimes_array, $allowed_extensions_array, $max_size = INF)
{
    $validation_ok = false;
    if (is_file($file_path_with_name) && ! empty($file_name) && ! empty($allowed_mimes_array) && ! empty($allowed_extensions_array)) {
        $file_info = finfo_open(FILEINFO_MIME_TYPE); //open file for validation
        $file_mime = strtolower(finfo_file($file_info, $file_path_with_name)); //get mime type
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); //get extension
        $file_size = filesize($file_path_with_name); //get size
        if (in_array($file_mime, arrayMapRecursive('strtolower', $allowed_mimes_array)) && in_array($file_extension, arrayMapRecursive('strtolower', $allowed_extensions_array)) && $file_size <= $max_size) {
            $validation_ok = true;
        }
    }

    return $validation_ok;
}

//check if file with specified name exists in specified directory and generate new name (with added number) if file exists already
function generateFileName($root_directory, $file_name)
{
    if (! empty($root_directory) && is_dir($root_directory) && ! empty($file_name)) {
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION); //get file extension
        $new_file_number = 2; //starting number to use for suffix of new file

        $files_list = array_map('strtolower', scandir($root_directory)); //get files list in root directory
        foreach ($files_list as $key => $value) {
            if (is_dir($value)) { //remove directories from list
                unset($files_list[$key]);
            }
        }

        while (in_array(strtolower($file_name), $files_list)) { //loop via files (don't use is_file because it's case sensitive, so if old file is file.ext and new file is File.ext, new file will be kept as File.ext instead of renaming into File-2.ext)
            if (preg_match_all('/-(\d+).'.$file_extension.'$/', $file_name, $old_numbers, PREG_SET_ORDER)) { //existing file already ends as *.-number.extension
                $old_file_number = end($old_numbers[0]); //get old number
                $new_file_number = $old_file_number + 1; //increase old number
                $file_name = preg_replace('/-(\d+).'.$file_extension.'$/', "-$new_file_number.$file_extension", $file_name); //replace old number with new number to avoid new filename being generated as *.old_number-new_number.extension
            } else { //file doesn't end as *.-number.extension, simply add a number to it
                $file_name = pathinfo($file_name, PATHINFO_FILENAME)."-$new_file_number.".$file_extension;
            }

            $new_file_number++;
        }
    }

    return $file_name;
}

//create slug from string
function slugifyText($string)
{
    if (! empty($string)) {
        if (function_exists('transliterator_transliterate')) { //transliterate string
            $string = transliterator_transliterate('Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; Lower();', $string);
        } else { //simply remove non-Latin letters
            $string = preg_replace('/[^A-Za-z0-9 ]/', '', $string);
        }
        $string = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $string)); //replace any remaining non-alphanumeric symbols with dashes
        while (substr($string, -1) == '-') { //remove excessive dashes (if any) from end of string
            $string = substr($string, 0, -1);
        }
    }

    return $string;
}

//apply array_map function recursively to array elements
function arrayMapRecursive($function_to_apply, $array)
{
    return filter_var($array, \FILTER_CALLBACK, ['options' => $function_to_apply]);
}
function extractDetailsOfSettings()
{
    $sets_array = DB::table('afl_settings')->get()->toArray();
    foreach ($sets_array as $set) {
        return $set;
    }
}

/**
 * This function return asset link based on link.php settings
 *
 * @param  string  $type
 * @param  string  $key
 * @return type
 */
function assetLink(string $type, string $key)
{
    // if request if language, it should append & language to it
    return asset(\Config::get('link.'.$type.'.'.$key));
}

/**
 * Gives bundle URL after appending version number to it
 *
 * @param  string  $url
 * @return string
 */
function bundleLink(string $url): string
{
    $baseUrl = asset($url).'?version='.\Config::get('app.tags');

    // if call is for language file, we should append language too in the url
    // REASON: we are sending cache headers while sending language response, which will improve performance since browser
    // will cache it. But as soon as language changes, language in cache will be same and will cause conflicts
    // adding language to argument will cause browser to request fresh response as soon as langauge changes
    // appending all activated plugin names too with the URL, so that if a plugin is activated, it requests a new
    // language file
    if (strpos($url, 'js/lang') !== false) {
        $baseUrl = $baseUrl.'&lang='.App::getLocale();
    }

    return $baseUrl;
}

/**
 * Check if white label plugin is enabled
 *
 * @return bool
 */
function isWhiteLabelEnabled()
{
    return is_dir(dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'Whitelabel');
}
function postEmailSendConfig($email,$title,$template,$data){
    try {

        $emailConfig = AflSettings::find(1);
        $mailController = new PhpMailController();

        $mailController->configSet($emailConfig);
        $mailController-> sendEmail($email, $title, $template, $data);
    } catch (\Exception $e) {
        return errorResponse($e, 500);
    }
}

function isInstall()
{
    $env = base_path('.env');
    return (File::exists($env) && env('DB_INSTALL') == 1);
}
function getAuthUserId()
{
    $bearerToken = request()->bearerToken();
    $tokenId = Configuration::forSymmetricSigner(new Signer\Blake2b(), InMemory::base64Encoded(base64_encode(str_random(60))))
        ->parser()
        ->parse($bearerToken)
        ->claims()
        ->get('jti');
    $userId = Passport::token()->where('id', $tokenId)->value('user_id');
    return $userId;
}

function getAuthUser()
{
    return AflClients::where('client_id',getAuthUserId())->first();
}
function statusFormatter($status)
{
    if (strtolower($status) == 'active'){
        $status = 1;
    }
    if (strtolower($status) == 'inactive' ){
        $status = 0;
    }
    return $status;
}

function successErrorFormatter($status)
{
    if (strtolower($status) == 'success'){
        $status = 1;
    }
    if (strtolower($status) == 'error' ){
        $status = 0;
    }
    return $status;
}


/**
 * Creates an empty DB with given name.
 *
 * @param  string  $dbName  name of the DB
 * @return null
 */
function createDB(string $dbName)
{
    try {
        \DB::purge('mysql');
        // removing old db
        \DB::connection('mysql')->getPdo()->exec("DROP DATABASE IF EXISTS `{$dbName}`");

        // Creating testing_db
        \DB::connection('mysql')->getPdo()->exec("CREATE DATABASE `{$dbName}`");
        //disconnecting it will remove database config from the memory so that new database name can be
        // populated
        \DB::disconnect('mysql');
    } catch (\Exception $e) {
        throw new \Exception("Database creation failed: " . $e->getMessage());
    }
}


/**
 * Validate API key and IP address.
 *
 * Returns null on success, or a error response if validation fails.
 *
 * @param string|null $api_key_secret
 * @param string|null $ip_address
 * @return string|null
 */
function validateApiKey(?string $api_key_secret, ?string $ip_address): ?string
{
    if (empty($api_key_secret)) {
        return Lang::get('lang.invalid_api_key');
    }

    $apiKey = AflApiKeys::where('api_key_secret', $api_key_secret)
        ->where('api_key_status', 1)
        ->first();

    if (empty($apiKey)) {
        return Lang::get('lang.invalid_api_key');
    }

    if (! empty($apiKey->api_key_ip)) {
        $allowedIps = array_map('trim', explode(',', $apiKey->api_key_ip));
        if (! in_array($ip_address, $allowedIps)) {
            return Lang::get('lang.Api_Acess_not_allowed');
        }
    }

    return null;
}
