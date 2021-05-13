<?php 


//check Auto Faveo Licenser core configuration and return an array with error messages if something wrong
function aflCheckSettings()
    {
    $notifications_array=array();
    

    if (empty(\config('constants.Basic.AFL_SALT')) || \config('constants.Basic.AFL_SALT') =="some_random_text") //invalid encryption salt
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_SALT');
        }

    if (!filter_var(\config('constants.Basic.AFL_ROOT_URL'), FILTER_VALIDATE_URL) || !ctype_alnum(substr(\config('constants.Basic.AFL_ROOT_URL'), -1))) //invalid Auto Faveo Licenser server URL
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_ROOT_URL');
        }

    if (!aflValidateIntegerValue(\config('constants.Basic.AFL_PRODUCT_ID'))) //invalid product ID
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_PRODUCT_ID');
        }

    if (!aflValidateIntegerValue(\config('constants.Basic.AFL_DAYS'), 1, 365)) //invalid verification period
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_VERIFICATION_PERIOD');
        }

    if (\config('constants.Basic.AFL_STORAGE')!="DATABASE" && \config('constants.Basic.AFL_STORAGE')!="FILE") //invalid license storage
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_STORAGE');
        }

    if (\config('constants.Basic.AFL_STORAGE')=="DATABASE" && !ctype_alnum(str_ireplace(array("_"), "", \config('constants.Basic.AFL_DATABASE_TABLE')))) //invalid license table name
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_TABLE');
        }

    if (\config('constants.Basic.AFL_STORAGE')=="FILE" && !@is_writable(\config('constants.Extra.AFL_DIRECTORY')."/".\config('constants.Basic.AFL_LICENSE_FILE_LOCATION'))) //invalid license file or permissions
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_LICENSE_FILE');
        }

    if (!empty(\config('constants.Advanced.AFL_ROOT_IP')) && !filter_var(\config('constants.Advanced.AFL_ROOT_IP'), FILTER_VALIDATE_IP)) //invalid Auto PHP Licenser server IP
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_ROOT_IP');
        }

    if (!empty(\config('constants.Advanced.AFL_ROOT_IP')) && !in_array(\config('constants.Advanced.AFL_ROOT_IP'), gethostbynamel(aflGetRawDomain(\config('constants.Basic.AFL_ROOT_URL'))))) //actual IP address of Auto PHP Licenser server doesn't match specified IP address
        {
        $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_DNS');
        }

    if (defined("APL_ROOT_NAMESERVERS") && !empty(\config('constants.Advanced.AFL_ROOT_NAMESERVERS'))) //check if nameservers are valid (use "defined" to check if nameservers are set because APL_ROOT_NAMESERVERS is commented by default to prevent errors in PHP<7)
        {
        foreach (\config('constants.Advanced.AFL_ROOT_NAMESERVERS') as $nameserver)
            {
            if (!aflValidateRawDomain($nameserver)) //invalid Auto PHP Licenser server nameservers
                {
                $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_ROOT_NAMESERVERS');
                break;
                }
            }
        }
      
    if (defined("APL_ROOT_NAMESERVERS") && !empty(\config('constants.Advanced.AFL_ROOT_NAMESERVERS'))) //check if actual nameservers of Auto PHP Licenser server domain match specified nameservers (use "defined" to check if nameservers are set because APL_ROOT_NAMESERVERS is commented by default to prevent errors in PHP<7)
        {
        $apl_root_nameservers_array=\config('constants.Advanced.AFL_ROOT_NAMESERVERS'); //create a variable from constant in order to use sort and other array functions
        $fetched_nameservers_array=array();

        $dns_records_array=dns_get_record(aflGetRawDomain(\config('constants.Basic.AFL_ROOT_URL')), DNS_NS);
        foreach ($dns_records_array as $record)
            {
            $fetched_nameservers_array[]=$record['target'];
            }

        $apl_root_nameservers_array=array_map("strtolower", $apl_root_nameservers_array); //convert root nameservers to lowercase
        $fetched_nameservers_array=array_map("strtolower", $fetched_nameservers_array); //convert fetched nameservers to lowercase

        sort($apl_root_nameservers_array); //sort both arrays before comparison
        sort($fetched_nameservers_array);
        if ($apl_root_nameservers_array!=$fetched_nameservers_array)
            {
            $notifications_array[]=\config('constants.NFD.AFL_CORE_NOTIFICATION_INVALID_DNS'); //actual nameservers of Auto PHP Licenser server don't match specified nameservers
            }
        }

    return $notifications_array;
    }






//make post requests with cookies and referrers, return array with server headers, errors, and body content
    function aflCustomPost($url, $post_info="", $refer="")
    {
    $user_agent="phpmillion cURL";
    $connect_timeout=10;
    $server_response_array=array();
    $formatted_headers_array=array();

    if (filter_var($url, FILTER_VALIDATE_URL) && !empty($post_info))
        {
        if (empty($refer) || !filter_var($refer, FILTER_VALIDATE_URL)) //use original URL as refer when no valid refer URL provided
            {
            $refer=$url;
            }

        $ch=curl_init();
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
            function($curl, $header) use (&$formatted_headers_array)
                {
                $len=strlen($header);
                $header=explode(":", $header, 2);
                if (count($header)<2) //ignore invalid headers
                return $len;

                $name=strtolower(trim($header[0]));
                $formatted_headers_array[$name]=trim($header[1]);

                return $len;
                }
            );

        $result=curl_exec($ch);
        $curl_error=curl_error($ch); //returns a human readable error (if any)
        curl_close($ch);

        $server_response_array['headers']=$formatted_headers_array;
        $server_response_array['error']=$curl_error;
        $server_response_array['body']=$result;
        }

    return $server_response_array;
    }




    
//process response from Auto PHP Licenser server. if response received, validate it and parse notifications and data (if any). if response not received or is invalid, return a corresponding notification
function aflParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
    {   
    $notifications_array=array();

    if (!empty($content_array)) //response received, validate it
        {
        if (!empty($content_array['headers']['notification_server_signature']) && aflVerifyServerSignature($content_array['headers']['notification_server_signature'], $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)) //response valid
            {
            $notifications_array['notification_case']=$content_array['headers']['notification_case'];
            $notifications_array['notification_text']=$content_array['headers']['notification_text'];
            if (!empty($content_array['headers']['notification_data'])) //additional data returned
                {
                $notifications_array['notification_data']=json_decode($content_array['headers']['notification_data'], true);
                }
            }
        else //response invalid
            {
            $notifications_array['notification_case']="notification_invalid_response";
            $notifications_array['notification_text']=config('constants.Basic.AFL_NOTIFICATION_INVALID_RESPONSE');
            }
        }
    else //no response received
        {
        $notifications_array['notification_case']="notification_no_connection";
        $notifications_array['notification_text']=config('constants.Basic.AFL_NOTIFICATION_NO_CONNECTION');
        }

    return $notifications_array;
    }



//verify signature received from Auto PHP Licenser server
function aflVerifyServerSignature($notification_server_signature, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
    {
    $result=false;
    $root_ips_array=gethostbynamel(aflGetRawDomain(config('constants.Basic.AFL_ROOT_URL')));

    if (!empty($notification_server_signature) && !empty($ROOT_URL) && isset($CLIENT_EMAIL) && isset($LICENSE_CODE) && !empty($root_ips_array))
        {
        if (hash("sha256", implode("", $root_ips_array).config('constants.Basic.AFL_PRODUCT_ID').$LICENSE_CODE.$CLIENT_EMAIL.$ROOT_URL.gmdate("Y-m-d"))==$notification_server_signature)
            {
            $result=true;
            }
        }

    return $result;
    }


    
//encrypt text with custom key
function aflCustomEncrypt($string, $key)
    {
    $encrypted_string="";

    if (!empty($string) && !empty($key))
        {
        $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc")); //generate an initialization vector

        $encrypted_string=openssl_encrypt($string, "aes-256-cbc", $key, 0, $iv); //encrypt the string using AES 256 encryption in CBC mode using encryption key and initialization vector
        $encrypted_string=base64_encode($encrypted_string."::".$iv); //the $iv is just as important as the key for decrypting, so save it with encrypted string using a unique separator "::"
        }

    return $encrypted_string;
    }



//validate integer and check if it's between min and max values
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
     * retrives the license data wheather it's stored in a database or file
     *
     * @return setting_row array consisting of license data
    */   
   function aflGetLicenseData(/*$MYSQLI_LINK=null*/)
    {
    $settings_row=array();

    if (config('constants.Basic.AFL_STORAGE')=="DATABASE") //license stored in database (use @ before mysqli_ function to prevent errors when function is executed by aplInstallLicense function)
        {

        //$settings_results=@mysqli_query($MYSQLI_LINK, "SELECT * FROM ".config('constants.Basic.AFL_DATABASE_TABLE'));
        $settings_row = FaveoLicense::all();

        }

    if (config('constants.Basic.AFL_STORAGE')=="FILE") //license stored in file
        {
        $settings_row=aflParseLicenseFile();
        }

    return $settings_row;
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