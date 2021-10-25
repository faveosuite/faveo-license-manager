<?php

//validate integer and check if it's between min and max values
use Illuminate\Support\Facades\DB;

function aflValidateIntegerValue($number, $min_value=1, $max_value=999999999)
    {
    $result=false;

    if (!is_float($number) && filter_var($number, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>$min_value, "max_range"=>$max_value)))!==false) //don't allow numbers like 1.0 to bypass validation
        {
        $result=true;
        }

    return $result;
    }



//get raw domain (returns (sub.)domain.com from url like http://www.(sub.)domain.com/something.php?xx=yy)
function aflGetRawDomain($url)
    {
    $raw_domain="";

    if (!empty($url))
        {
        $scheme=parse_url($url, PHP_URL_SCHEME); //check if scheme exists because URL can't be parsed properly without a scheme
        if (empty($scheme)) //add a temporary http:// scheme before parsing if needed
            {
            $url="http://".$url;
            }

        $raw_domain=str_ireplace("www.", "", parse_url($url, PHP_URL_HOST));
        }

    return $raw_domain;
    }




//validate raw domain (only URL like (sub.)domain.com will validate)
function aflValidateRawDomain($url)
    {
    $result=false;

    if (!empty($url))
        {
        if (preg_match('/^[a-z0-9-.]+\.[a-z\.]{2,7}$/', strtolower($url))) //check if this is valid tld
            {
            $result=true;
            }
        }

    return $result;
    }




    function errorResponse($message, $statusCode = FAVEO_ERROR_CODE) {
    /**
     * When developers simply want to send info of exceptions they are handling
     * using errorResponse() and explicitly pass $e->getCode() as second argument
     * then if thrown exception has code as 0 then response() will throw an error
     * and response will be modified to new exception. We are handling this here
     * so that developers can easily use this method and simply pass $e->getCode()
     * to use for response HTTP code.
     */
    $statusCode = ($statusCode)?:FAVEO_ERROR_CODE;
    return response()->json(['success' => false, 'message' => $message], $statusCode);
}


/**
 * Format success message/data into json success response
 *
 * @param string $message Success message
 * @param array|string $data Data of the response
 * @param int $statusCode
 * @return \Illuminate\Http\JsonResponse json response
 */
function successResponse($message = '', $data = '', $statusCode = FAVEO_SUCCESS_CODE) {
    $response = ['success' => true];

    // if message given
    if (!empty($message)) {
        $response['message'] = $message;
    }

    // If data given
    if (!empty($data)) {
        $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
}


      /**
     * verify date and/or time according to provided format (such as Y-m-d, Y-m-d H:i, H:i, and so on)
     * @param $datetime
     * @param $format
     * @return $result
    */
     function aflVerifyDateTime($datetime, $format)
    {
    $result=false;

    if (!empty($datetime) && !empty($format))
        {
        $datetime=DateTime::createFromFormat($format, $datetime);
        $errors=DateTime::getLastErrors();

        if ($datetime && empty($errors['warning_count'])) //datetime OK
            {
            $result=true;
            }
        }

    return $result;
    }


//create report
function createReport($report_text, $account_id, $report_system, $report_status)
    {
    if (!empty($report_text) && aflValidateIntegerValue($report_system, 0, 1))
        {
        $report_date_time=date("Y-m-d H:i:s");

       // doMysqlQuery("INSERT IGNORE INTO apl_reports (account_id, report_date_time, report_text, report_system, report_status) VALUES (?, ?, ?, ?, ?)", array($account_id, $report_date_time, $report_text, $report_system, $report_status), array("i", "s", "s", "i", "i"));
       try{
           DB::table('afl_reports')->insertOrIgnore([
               'account_id' => $account_id,
               'report_date_time' => $report_date_time,
               'report_text' => $report_text,
               'report_system' => $report_system,
               'report_status' => $report_status
           ]);
           return 1;
       }
       catch(Exception $e){
          return 0;
       }
        }
    }

//set default start date
function setDefaultDateFrom($RECORDS_HIDE_DAYS)
    {
    if (aflValidateIntegerValue($RECORDS_HIDE_DAYS)) //hide records older than RECORDS_HIDE_DAYS days
        {
        $date_from=date("Y-m-d", strtotime("-$RECORDS_HIDE_DAYS days"));
        }
    else //set start date empty (all records will be included)
        {
        $date_from="";
        }

    return $date_from;
    }

//format client
function formatClient($license_code, $client_email)
    {
    if (!empty($license_code))
        {
        $client_formatted=$license_code;
        }
    else
        {
        if (filter_var($client_email, FILTER_VALIDATE_EMAIL))
            {
            $client_formatted=$client_email;
            }
        else
            {
            $client_formatted="Unknown Client";
            }
        }

    return $client_formatted;
    }

//remove seconds from (date)timestamp
function removeSeconds($timestamp)
    {
    if (!empty($timestamp) && (aflVerifyDateTime($timestamp, "Y-m-d H:i:s") ||aflVerifyDateTime($timestamp, "H:i:s")))
        {
        $timestamp=substr($timestamp, 0, -3);
        }

    return $timestamp;
    }
//format and return array with item status class and text
function returnFormattedStatusArray($status, $active_text="Active", $inactive_text="Inactive", $other_text="Other", $unknown_text="Unknown", $null_text="")
    {
    if (is_null($status)) //don't apply formatting at all
        {
        $item_array['status_class']="";
        $item_array['status_text']=$null_text;
        }
    elseif (isZero($status)) //inactive
        {
        $item_array['status_class']="text-red";
        $item_array['status_text']=$inactive_text;
        }
    elseif ($status==1) //active
        {
        $item_array['status_class']="text-green";
        $item_array['status_text']=$active_text;
        }
    elseif ($status==2) //other
        {
        $item_array['status_class']="text-yellow";
        $item_array['status_text']=$other_text;
        }
    else //unknown
        {
        $item_array['status_class']="text-blue";
        $item_array['status_text']=$unknown_text;
        }

    return $item_array;
    }



//format array with report status text and class
function returnFormattedReportStatusArray($status, $success_text="Success", $error_text="Error", $warning_text="Warning", $unknown_text="Unknown")
    {
    if (isZero($status)) //error
        {
        $item_array['status_class']="text-red";
        $item_array['status_text']=$error_text;
        }
    elseif ($status==1) //success
        {
        $item_array['status_class']="text-green";
        $item_array['status_text']=$success_text;
        }
    elseif ($status==2) //warning
        {
        $item_array['status_class']="text-yellow";
        $item_array['status_text']=$warning_text;
        }
    else //unknown
        {
        $item_array['status_class']="text-blue";
        $item_array['status_text']=$unknown_text;
        }

    return $item_array;
    }

//check if argument is equal to zero (only returns true if argument is 0 or "0", returns false when argument is empty or null)
function isZero($argument)
    {
    $result=false;

    if (strlen($argument)==1 && filter_var($argument, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>0, "max_range"=>0)))!==false)
        {
        $result=true;
        }

    return $result;
    }

//generate secure random string
function generateRandomString($string_length=0)
    {
    if (!aflValidateIntegerValue($string_length))
        {
        $string_length=mt_rand(16,64);
        }

    $random_string=substr(bin2hex(openssl_random_pseudo_bytes($string_length)), 0, $string_length); //bin2hex makes string twice longer, truncate it to specified length

    return $random_string;
    }

//parse license file and make an array with license data
function aflParseLicenseFile()
    {
    $license_data_array=array();

    if (@is_readable(config('constants.Extra.AFL_DIRECTORY')."/". config('constants.Basic.AFL_LICENSE_FILE_LOCATION')))
        {
        $file_content=file_get_contents(config('constants.Extra.AFL_DIRECTORY')."/".config('constants.Basic.AFL_LICENSE_FILE_LOCATION'));
        preg_match_all("/<([A-Z_]+)>(.*?)<\/([A-Z_]+)>/", $file_content, $matches, PREG_SET_ORDER);
        if (!empty($matches))
            {
            foreach ($matches as $value)
                {
                if (!empty($value[1]) && $value[1]==$value[3])
                    {
                    $license_data_array[$value[1]]=$value[2];
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
     *
     * @return Hashed $script_signature
    */
    function aflGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
    {
    $script_signature="";
    $root_ips_array=gethostbynamel(aflGetRawDomain(config('constants.Basic.AFL_ROOT_URL')));

    if (!empty($ROOT_URL) && isset($CLIENT_EMAIL) && isset($LICENSE_CODE) && !empty($root_ips_array))
        {
        $script_signature=Hash::make(gmdate("Y-m-d").$ROOT_URL.$CLIENT_EMAIL.$LICENSE_CODE.config('constants.Basic.AFL_PRODUCT_ID').implode("", $root_ips_array));
        }

    return $script_signature;
    }


//format and return nice numbers dropdown array
function returnNumbersDropdownArray($numbers_array, $title, $disabled_title, $selected_value)
{
    $root_array=array();

    if (is_array($numbers_array) && !empty($title) && !empty($disabled_title))
    {
        foreach ($numbers_array as $key=>$value)
        {
            $item_array['value']=$value;
            $item_array['title']=$value; //use $value as option title by default

            if (isZero($item_array['value']) && !empty($disabled_title)) //format disabled title when value is 0
            {
                $item_array['title']=$disabled_title;
            }

            if ($item_array['value']>0 && !empty($title)) //format title by adding a specific word to it
            {
                $item_array['title'].=" $title";

                if ($item_array['value']==1 && substr($item_array['title'], -1)=="s") //remove last "s" if needed, so "1 records" becomes "1 record"
                {
                    $item_array['title']=substr($item_array['title'], 0, -1);
                }
            }

            $item_array['selected']=returnOptionStatus($item_array['value'], $selected_value);

            $root_array[]=$item_array;
        }
    }

    return $root_array;
}

//return selection status for dropdown option
function returnOptionStatus($current_value, $selected_values_array, $readonly=0)
{
    $status="";

    if (in_array(strval($current_value), array_map("strval", convertVariableToArray($selected_values_array)))) //convert ID(s) to array, so function works with single-select and multi-select dropdowns. also compare both values as strings to avoid false positive when current_value is some string and selected_values_array contains 0
    {
        $status=" selected";
    }
    else
    {
        if ($readonly==1) //mark non-selected option as disabled (read-only)
        {
            $status=" disabled";
        }
    }

    return $status;
}

//convert variables into arrays
function convertVariableToArray($var_name)
{
    if (!is_array($var_name))
    {
        $var_name=array($var_name);
    }

    return $var_name;
}
//return time zones
function returnTimezonesDropdownArray($TIMEZONE)
{
    $root_array=array();

    $timezones_array=array("Africa/Abidjan", "Africa/Accra", "Africa/Addis_Ababa", "Africa/Algiers", "Africa/Asmara", "Africa/Bamako", "Africa/Bangui", "Africa/Banjul", "Africa/Bissau", "Africa/Blantyre", "Africa/Brazzaville", "Africa/Bujumbura", "Africa/Cairo", "Africa/Casablanca", "Africa/Ceuta", "Africa/Conakry", "Africa/Dakar", "Africa/Dar_es_Salaam", "Africa/Djibouti", "Africa/Douala", "Africa/El_Aaiun", "Africa/Freetown", "Africa/Gaborone", "Africa/Harare", "Africa/Johannesburg", "Africa/Juba", "Africa/Kampala", "Africa/Khartoum", "Africa/Kigali", "Africa/Kinshasa", "Africa/Lagos", "Africa/Libreville", "Africa/Lome", "Africa/Luanda", "Africa/Lubumbashi", "Africa/Lusaka", "Africa/Malabo", "Africa/Maputo", "Africa/Maseru", "Africa/Mbabane", "Africa/Mogadishu", "Africa/Monrovia", "Africa/Nairobi", "Africa/Ndjamena", "Africa/Niamey", "Africa/Nouakchott", "Africa/Ouagadougou", "Africa/Porto-Novo", "Africa/Sao_Tome", "Africa/Tripoli", "Africa/Tunis", "Africa/Windhoek", "America/Adak", "America/Anchorage", "America/Anguilla", "America/Antigua", "America/Araguaina", "America/Argentina/Buenos_Aires", "America/Argentina/Catamarca", "America/Argentina/Cordoba", "America/Argentina/Jujuy", "America/Argentina/La_Rioja", "America/Argentina/Mendoza", "America/Argentina/Rio_Gallegos", "America/Argentina/Salta", "America/Argentina/San_Juan", "America/Argentina/San_Luis", "America/Argentina/Tucuman", "America/Argentina/Ushuaia", "America/Aruba", "America/Asuncion", "America/Atikokan", "America/Bahia", "America/Bahia_Banderas", "America/Barbados", "America/Belem", "America/Belize", "America/Blanc-Sablon", "America/Boa_Vista", "America/Bogota", "America/Boise", "America/Cambridge_Bay", "America/Campo_Grande", "America/Cancun", "America/Caracas", "America/Cayenne", "America/Cayman", "America/Chicago", "America/Chihuahua", "America/Costa_Rica", "America/Creston", "America/Cuiaba", "America/Curacao", "America/Danmarkshavn", "America/Dawson", "America/Dawson_Creek", "America/Denver", "America/Detroit", "America/Dominica", "America/Edmonton", "America/Eirunepe", "America/El_Salvador", "America/Fortaleza", "America/Glace_Bay", "America/Godthab", "America/Goose_Bay", "America/Grand_Turk", "America/Grenada", "America/Guadeloupe", "America/Guatemala", "America/Guayaquil", "America/Guyana", "America/Halifax", "America/Havana", "America/Hermosillo", "America/Indiana/Indianapolis", "America/Indiana/Knox", "America/Indiana/Marengo", "America/Indiana/Petersburg", "America/Indiana/Tell_City", "America/Indiana/Vevay", "America/Indiana/Vincennes", "America/Indiana/Winamac", "America/Inuvik", "America/Iqaluit", "America/Jamaica", "America/Juneau", "America/Kentucky/Louisville", "America/Kentucky/Monticello", "America/Kralendijk", "America/La_Paz", "America/Lima", "America/Los_Angeles", "America/Lower_Princes", "America/Maceio", "America/Managua", "America/Manaus", "America/Marigot", "America/Martinique", "America/Matamoros", "America/Mazatlan", "America/Menominee", "America/Merida", "America/Metlakatla", "America/Mexico_City", "America/Miquelon", "America/Moncton", "America/Monterrey", "America/Montevideo", "America/Montserrat", "America/Nassau", "America/New_York", "America/Nipigon", "America/Nome", "America/Noronha", "America/North_Dakota/Beulah", "America/North_Dakota/Center", "America/North_Dakota/New_Salem", "America/Ojinaga", "America/Panama", "America/Pangnirtung", "America/Paramaribo", "America/Phoenix", "America/Port-au-Prince", "America/Port_of_Spain", "America/Porto_Velho", "America/Puerto_Rico", "America/Rainy_River", "America/Rankin_Inlet", "America/Recife", "America/Regina", "America/Resolute", "America/Rio_Branco", "America/Santa_Isabel", "America/Santarem", "America/Santiago", "America/Santo_Domingo", "America/Sao_Paulo", "America/Scoresbysund", "America/Sitka", "America/St_Barthelemy", "America/St_Johns", "America/St_Kitts", "America/St_Lucia", "America/St_Thomas", "America/St_Vincent", "America/Swift_Current", "America/Tegucigalpa", "America/Thule", "America/Thunder_Bay", "America/Tijuana", "America/Toronto", "America/Tortola", "America/Vancouver", "America/Whitehorse", "America/Winnipeg", "America/Yakutat", "America/Yellowknife", "Antarctica/Casey", "Antarctica/Davis", "Antarctica/DumontDUrville", "Antarctica/Macquarie", "Antarctica/Mawson", "Antarctica/McMurdo", "Antarctica/Palmer", "Antarctica/Rothera", "Antarctica/Syowa", "Antarctica/Troll", "Antarctica/Vostok", "Arctic/Longyearbyen", "Asia/Aden", "Asia/Almaty", "Asia/Amman", "Asia/Anadyr", "Asia/Aqtau", "Asia/Aqtobe", "Asia/Ashgabat", "Asia/Baghdad", "Asia/Bahrain", "Asia/Baku", "Asia/Bangkok", "Asia/Beirut", "Asia/Bishkek", "Asia/Brunei", "Asia/Chita", "Asia/Choibalsan", "Asia/Colombo", "Asia/Damascus", "Asia/Dhaka", "Asia/Dili", "Asia/Dubai", "Asia/Dushanbe", "Asia/Gaza", "Asia/Hebron", "Asia/Ho_Chi_Minh", "Asia/Hong_Kong", "Asia/Hovd", "Asia/Irkutsk", "Asia/Jakarta", "Asia/Jayapura", "Asia/Jerusalem", "Asia/Kabul", "Asia/Kamchatka", "Asia/Karachi", "Asia/Kathmandu", "Asia/Khandyga", "Asia/Kolkata", "Asia/Krasnoyarsk", "Asia/Kuala_Lumpur", "Asia/Kuching", "Asia/Kuwait", "Asia/Macau", "Asia/Magadan", "Asia/Makassar", "Asia/Manila", "Asia/Muscat", "Asia/Nicosia", "Asia/Novokuznetsk", "Asia/Novosibirsk", "Asia/Omsk", "Asia/Oral", "Asia/Phnom_Penh", "Asia/Pontianak", "Asia/Pyongyang", "Asia/Qatar", "Asia/Qyzylorda", "Asia/Rangoon", "Asia/Riyadh", "Asia/Sakhalin", "Asia/Samarkand", "Asia/Seoul", "Asia/Shanghai", "Asia/Singapore", "Asia/Srednekolymsk", "Asia/Taipei", "Asia/Tashkent", "Asia/Tbilisi", "Asia/Tehran", "Asia/Thimphu", "Asia/Tokyo", "Asia/Ulaanbaatar", "Asia/Urumqi", "Asia/Ust-Nera", "Asia/Vientiane", "Asia/Vladivostok", "Asia/Yakutsk", "Asia/Yekaterinburg", "Asia/Yerevan", "Atlantic/Azores", "Atlantic/Bermuda", "Atlantic/Canary", "Atlantic/Cape_Verde", "Atlantic/Faroe", "Atlantic/Madeira", "Atlantic/Reykjavik", "Atlantic/South_Georgia", "Atlantic/St_Helena", "Atlantic/Stanley", "Australia/Adelaide", "Australia/Brisbane", "Australia/Broken_Hill", "Australia/Currie", "Australia/Darwin", "Australia/Eucla", "Australia/Hobart", "Australia/Lindeman", "Australia/Lord_Howe", "Australia/Melbourne", "Australia/Perth", "Australia/Sydney", "Europe/Amsterdam", "Europe/Andorra", "Europe/Athens", "Europe/Belgrade", "Europe/Berlin", "Europe/Bratislava", "Europe/Brussels", "Europe/Bucharest", "Europe/Budapest", "Europe/Busingen", "Europe/Chisinau", "Europe/Copenhagen", "Europe/Dublin", "Europe/Gibraltar", "Europe/Guernsey", "Europe/Helsinki", "Europe/Isle_of_Man", "Europe/Istanbul", "Europe/Jersey", "Europe/Kaliningrad", "Europe/Kiev", "Europe/Lisbon", "Europe/Ljubljana", "Europe/London", "Europe/Luxembourg", "Europe/Madrid", "Europe/Malta", "Europe/Mariehamn", "Europe/Minsk", "Europe/Monaco", "Europe/Moscow", "Europe/Oslo", "Europe/Paris", "Europe/Podgorica", "Europe/Prague", "Europe/Riga", "Europe/Rome", "Europe/Samara", "Europe/San_Marino", "Europe/Sarajevo", "Europe/Simferopol", "Europe/Skopje", "Europe/Sofia", "Europe/Stockholm", "Europe/Tallinn", "Europe/Tirane", "Europe/Uzhgorod", "Europe/Vaduz", "Europe/Vatican", "Europe/Vienna", "Europe/Vilnius", "Europe/Volgograd", "Europe/Warsaw", "Europe/Zagreb", "Europe/Zaporozhye", "Europe/Zurich", "Indian/Antananarivo", "Indian/Chagos", "Indian/Christmas", "Indian/Cocos", "Indian/Comoro", "Indian/Kerguelen", "Indian/Mahe", "Indian/Maldives", "Indian/Mauritius", "Indian/Mayotte", "Indian/Reunion", "Pacific/Apia", "Pacific/Auckland", "Pacific/Bougainville", "Pacific/Chatham", "Pacific/Chuuk", "Pacific/Easter", "Pacific/Efate", "Pacific/Enderbury", "Pacific/Fakaofo", "Pacific/Fiji", "Pacific/Funafuti", "Pacific/Galapagos", "Pacific/Gambier", "Pacific/Guadalcanal", "Pacific/Guam", "Pacific/Honolulu", "Pacific/Johnston", "Pacific/Kiritimati", "Pacific/Kosrae", "Pacific/Kwajalein", "Pacific/Majuro", "Pacific/Marquesas", "Pacific/Midway", "Pacific/Nauru", "Pacific/Niue", "Pacific/Norfolk", "Pacific/Noumea", "Pacific/Pago_Pago", "Pacific/Palau", "Pacific/Pitcairn", "Pacific/Pohnpei", "Pacific/Port_Moresby", "Pacific/Rarotonga", "Pacific/Saipan", "Pacific/Tahiti", "Pacific/Tarawa", "Pacific/Tongatapu", "Pacific/Wake", "Pacific/Wallis", "UTC");

    foreach ($timezones_array as $key=>$value)
    {
        $item_array['value']=$value;
        $item_array['title']=$value;
        $item_array['selected']=returnOptionStatus($item_array['value'], $TIMEZONE);

        $root_array[]=$item_array;
    }

    return $root_array;
}
