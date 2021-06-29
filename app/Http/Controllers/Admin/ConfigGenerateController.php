<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ConfigRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;



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
    public function configGenerate(ConfigRequest $request){


        $error_detected=0;
        $product_id = $request->get('product_id');
        $config_afl_days = $request->get('License_Verification_Period');
        $config_afl_storage = $request->get('License_Storage_type');
        $config_afl_database_table = $request->get('MySQL_Table_Name');
        $config_afl_license_file_location = $request->get('Database_License_File_Location');
        $cancel_license = $request->get('Delete_Cancelled_License');
        $cracked_license =$request->get('Delete_Cracked_License');
        $god_mode = $request->get('God_Mode');

        
    $ROOT_URL = "https://www.demo.phpmillion.com/apl"; // need to get it from faveo billing so this is temporary to check if this api is working

    if (!isset($product_id) || !aflValidateIntegerValue($product_id))
    {
    $product_id=0;
    }
    if (!isset($config_afl_days) || !aflValidateIntegerValue($config_afl_days))
    {
    $config_afl_days=7;
    }
    if (!isset($config_afl_license_file_location))
    {
    $config_afl_license_file_location="signature/license.key.example";
    }

    if (!isset($config_afl_database_table))
    {
    $config_afl_database_table="user_data";
    }

        if (aflValidateIntegerValue($product_id) && 
            aflValidateIntegerValue($config_afl_days, 1, 365) && 
            in_array($config_afl_storage, array("DATABASE", "FILE")) && 
            !empty($config_afl_database_table) && 
            !empty($config_afl_license_file_location))
        {


         $product = DB::table('afl_products')->where('product_id',$product_id)->get()->toArray();
         if(empty($product))
         {
            $error_detected = 1;
            return errorResponse(Lang::get('lang.invalid'),400);
         }              
        
        if($error_detected!=1){
             
            $config_file_content=file_get_contents("afl_core_configuration.php"); //get example content

            //do replace
            $config_values_bad_array=array('/define\("AFL_SALT", "([A-Za-z0-9_]+)"\);/', 
                                           '/define\("AFL_ROOT_URL", ".*"\);/', 
                                           '/define\("AFL_PRODUCT_ID", [0-9]+\);/', 
                                           '/define\("AFL_DAYS", ([0-9]+)\);/', 
                                           '/define\("AFL_STORAGE", "([A-Za-z]+)"\);/', 
                                           '/define\("AFL_DATABASE_TABLE", "([A-Za-z0-9_]+)"\);/', 
                                           '/define\("AFL_LICENSE_FILE_LOCATION", ".*"\);/', 
                                           '/define\("AFL_INCLUDE_KEY_CONFIG", "([A-Za-z0-9_]+)"\);/', 
                                           '/define\("AFL_DELETE_CANCELLED", ".*"\);/', 
                                           '/define\("AFL_DELETE_CRACKED", ".*"\);/', 
                                           '/define\("AFL_GOD_MODE", ".*"\);/');

            $config_values_good_array=array('define("AFL_SALT", "'.generateRandomString(16).'");', 
                                            'define("AFL_ROOT_URL", "'.$ROOT_URL.'");', 
                                            'define("AFL_PRODUCT_ID", '.$product_id.');', 
                                            'define("AFL_DAYS", '.$config_afl_days.');', 
                                            'define("AFL_STORAGE", "'.$config_afl_storage.'");', 
                                            'define("AFL_DATABASE_TABLE", "'.$config_afl_database_table.'");', 
                                            'define("AFL_LICENSE_FILE_LOCATION", "'.$config_afl_license_file_location.'");', 
                                            'define("AFL_INCLUDE_KEY_CONFIG", "'.generateRandomString(16).'");', 
                                            'define("AFL_DELETE_CANCELLED", "'.$cancel_license.'");', 
                                            'define("AFL_DELETE_CRACKED", "'.$cracked_license.'");', 
                                            'define("AFL_GOD_MODE", "'.$god_mode.'");');

            $config_file_content=preg_replace($config_values_bad_array, $config_values_good_array, $config_file_content);


            if (empty($config_file_content)) //no content
                {
                $error_detected=1;
                return errorResponse(Lang::get('lang.invalid'),400);
                }
            else //everything OK
                {
                return successResponse(Lang::get('lang.config'),$config_file_content,200);
                }
        }
         }
         else{
             $error_detected =1;
             return  errorResponse(Lang::get('lang.no_config'),400);
         } 
        
    }
}
