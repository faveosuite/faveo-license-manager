<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ConfigRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\AflProducts;
use Illuminate\Support\Facades\File;



/**
 * Consist of functionalities for the Configuration Generator page in Auto Faveo licenser
 * Class ConfigGenerateController
 * @package App\Http\Controllers\Admin
 */
class ConfigGenerateController extends Controller
{

    /**
     * To generate a afl_core_configurations file
     * @param ConfigRequest $request
     * @return the details that has to be updated or added to afl_core_configurations
     */
    public function configGenerate(ConfigRequest $request)
    {

        $action_success=0; //will be changed to 1 later only if everything OK
        $error_detected=0; //will be changed to 1 later if error occurs
        $error_details=""; //will be filled with errors (if any)
        $product_id = $request->get('product_id');
        $config_afl_days = $request->get('License_Verification_Period');
        $config_afl_storage = $request->get('License_Storage_type');
        $config_afl_database_table = $request->get('MySQL_Table_Name');
        $config_afl_license_file_location = $request->get('Database_License_File_Location');
        $config_afl_delete_cancelled = $request->get('Delete_Cancelled_License');
        $config_afl_delete_cracked =$request->get('Delete_Cracked_License');
        $config_afl_god_mode = $request->get('God_Mode');
        $config_aus_connection_timeout = $request->get('Connection_Timeout');
        $config_aus_delete_extracted = $request->get('Delete_Downloaded_Archive_After_Extracting');
         //get script settings
         $settingDetails=extractDetailsOfSettings();
         extract((array)$settingDetails);
        // need to get it from faveo billing so this is temporary to check if this api is working
        if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($config_afl_days, 1, 365) &&
            in_array($config_afl_storage, array("DATABASE", "FILE")) && !empty($config_afl_database_table) &&
            !empty($config_afl_license_file_location) && aflValidateIntegerValue($config_aus_connection_timeout))
            {

        if (empty($rows_array=AflProducts::where('product_id',$product_id)->get()->toArray())) //invalid record
            {
                $error_details="Invalid Product.";
            }
        if ($error_detected!=1)
            {
            foreach ($rows_array as $row)
                {
                extract((array)$row);
                }
            $config_file_content=$this->generateFileContents($ROOT_URL,$product_id,$config_afl_days,$config_afl_storage,$config_afl_database_table,$config_afl_license_file_location,$config_afl_delete_cancelled,$config_afl_delete_cracked,$config_afl_god_mode,$product_key,$config_aus_connection_timeout,$config_aus_delete_extracted);
            if (empty($config_file_content)) //no content
                {
                $error_detected=1;
                $error_details.="Sample configuration file is empty.";
                }
            else //everything OK
                {
                $action_success=1;
                }
            }
        }
    else
        {
        $error_detected=1;
        $error_details.="Invalid product, license verification period, license storage type, license file location or MySQL table name.";
        }

    if ($action_success==1) //everything OK
        {
        $page_message="$product_title configuration file generated.";
        }
    else //display error message
        {
        $page_message="The configuration file could not be generated because of this reason: $error_details";
        createReport(strip_tags($page_message), 1, 1, $action_success);
        return response(['Error Message' => $page_message]);
        }
    createReport(strip_tags($page_message), 1, 1, $action_success);
    return $config_file_content;
}

//generates the config file contents
protected function generateFileContents($ROOT_URL,$product_id,$config_afl_days,$config_afl_storage,$config_afl_database_table,$config_afl_license_file_location,$config_afl_delete_cancelled,$config_afl_delete_cracked,$config_afl_god_mode,$product_key,$config_aus_connection_timeout,$config_aus_delete_extracted){
        $path = public_path();
    $config_file_content=File::get($path.DIRECTORY_SEPARATOR.'afl_core_configuration.php');
    //do replace
    $config_values_bad_array=array('/define\("APL_SALT", "([A-Za-z0-9_]+)"\);/', '/define\("APL_ROOT_URL", ".*"\);/', '/define\("APL_PRODUCT_ID", [0-9]+\);/', '/define\("APL_DAYS", ([0-9]+)\);/', '/define\("APL_STORAGE", "([A-Za-z]+)"\);/', '/define\("APL_DATABASE_TABLE", "([A-Za-z0-9_]+)"\);/', '/define\("APL_LICENSE_FILE_LOCATION", ".*"\);/', '/define\("APL_INCLUDE_KEY_CONFIG", "([A-Za-z0-9_]+)"\);/', '/define\("APL_DELETE_CANCELLED", ".*"\);/', '/define\("APL_DELETE_CRACKED", ".*"\);/', '/define\("APL_GOD_MODE", ".*"\);/','/define\("AUS_PRODUCT_KEY", "([A-Za-z0-9_]+)"\);/', '/define\("AUS_CONNECTION_TIMEOUT", [0-9]+\);/', '/define\("AUS_DELETE_EXTRACTED", "(.*?)"\);/');
    $config_values_good_array=array('define("APL_SALT", "'.generateRandomString(16).'");', 'define("APL_ROOT_URL", "'.$ROOT_URL.'");', 'define("APL_PRODUCT_ID", '.$product_id.');', 'define("APL_DAYS", '.$config_afl_days.');', 'define("APL_STORAGE", "'.$config_afl_storage.'");', 'define("APL_DATABASE_TABLE", "'.$config_afl_database_table.'");', 'define("APL_LICENSE_FILE_LOCATION", "'.$config_afl_license_file_location.'");', 'define("APL_INCLUDE_KEY_CONFIG", "'.generateRandomString(16).'");', 'define("APL_DELETE_CANCELLED", "'.$config_afl_delete_cancelled.'");', 'define("APL_DELETE_CRACKED", "'.$config_afl_delete_cracked.'");', 'define("APL_GOD_MODE", "'.$config_afl_god_mode.'");','define("AUS_PRODUCT_KEY", "'.$product_key.'");', 'define("AUS_CONNECTION_TIMEOUT", '.$config_aus_connection_timeout.');', 'define("AUS_DELETE_EXTRACTED", "'.$config_aus_delete_extracted.'");');
    $config_file_content=preg_replace($config_values_bad_array, $config_values_good_array, $config_file_content);
    return $config_file_content;
}

//return selection status for dropdown option
private function returnOptionStatus($current_value, $selected_values_array, $readonly=0)
    {
    $status="";
    if (in_array(strval($current_value), array_map("strval", $this->convertVariableToArray($selected_values_array)))) //convert ID(s) to array, so function works with single-select and multi-select dropdowns. also compare both values as strings to avoid false positive when current_value is some string and selected_values_array contains 0
        {
        $status="selected";
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
private function convertVariableToArray($var_name)
    {
    if (!is_array($var_name))
        {
        $var_name=array($var_name);
        }
    return $var_name;
    }

//return readonly status
private function returnReadOnlyStatus($forced_readonly, $status)
    {
    $readonly=0;
    if ($forced_readonly==1 || isZero($status)) //forced readonly applied or item status is inactive, make it readonly
        {
        $readonly=1;
        }
    return $readonly;
    }
}
