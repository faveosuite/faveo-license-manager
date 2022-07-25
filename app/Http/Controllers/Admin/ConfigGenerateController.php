<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigRequest;
use App\Models\AflProducts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the Configuration Generator page in Auto Faveo licenser
 * Class ConfigGenerateController
 */
class ConfigGenerateController extends Controller
{
    /**
     * To generate a afl_core_configurations file
     *
     * @param  ConfigRequest  $request
     * @return the details that has to be updated or added to afl_core_configurations
     */
    public function configGenerate(ConfigRequest $request)
    {
        try {
            $action_success = 0; //will be changed to 1 later only if everything OK
    $error_detected = 0; //will be changed to 1 later if error occurs
    $error_details = ''; //will be filled with errors (if any)
    $added_records = 0;
            $updated_records = 0;
            $removed_records = 0;
            $product_id = $request->get('product_id');
            $config_afl_days = $request->get('License_Verification_Period');
            $config_afl_storage = $request->get('License_Storage_type');
            $config_afl_database_table = $request->get('MySQL_Table_Name');
            $config_afl_license_file_location = $request->get('Database_License_File_Location');
            $config_afl_delete_cancelled = $request->get('Delete_Cancelled_License');
            $config_afl_delete_cracked = $request->get('Delete_Cracked_License');
            $config_afl_god_mode = $request->get('God_Mode');
            //get script settings
            $sets_array = DB::table('afl_settings')->get()->toArray();
            foreach ($sets_array as $set) {
                extract((array) $set);
            }

            // need to get it from faveo billing so this is temporary to check if this api is working
            if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($config_afl_days, 1, 365) && in_array($config_afl_storage, ['DATABASE', 'FILE']) && ! empty($config_afl_database_table) && ! empty($config_afl_license_file_location)) {
                if (empty($rows_array = DB::table('afl_products')->where('product_id', $product_id)->get()->toArray())) { //invalid record
                    $error_detected = 1;
                    $error_details .= 'Invalid product.<br>';

                    return errorResponse(Lang::get('lang.invalid'), 404);
                }

                if ($error_detected != 1) {
                    foreach ($rows_array as $row) {
                        extract((array) $row);
                    }

                    $config_file_content = @file_get_contents('afl_core_configuration.php');

                    //get example content
                    //do replace
                    $config_values_bad_array = ['/define\("APL_SALT", "([A-Za-z0-9_]+)"\);/', '/define\("APL_ROOT_URL", ".*"\);/', '/define\("APL_PRODUCT_ID", [0-9]+\);/', '/define\("APL_DAYS", ([0-9]+)\);/', '/define\("APL_STORAGE", "([A-Za-z]+)"\);/', '/define\("APL_DATABASE_TABLE", "([A-Za-z0-9_]+)"\);/', '/define\("APL_LICENSE_FILE_LOCATION", ".*"\);/', '/define\("APL_INCLUDE_KEY_CONFIG", "([A-Za-z0-9_]+)"\);/', '/define\("APL_DELETE_CANCELLED", ".*"\);/', '/define\("APL_DELETE_CRACKED", ".*"\);/', '/define\("APL_GOD_MODE", ".*"\);/'];
                    $config_values_good_array = ['define("APL_SALT", "'.generateRandomString(16).'");', 'define("APL_ROOT_URL", "'.$ROOT_URL.'");', 'define("APL_PRODUCT_ID", '.$product_id.');', 'define("APL_DAYS", '.$config_afl_days.');', 'define("APL_STORAGE", "'.$config_afl_storage.'");', 'define("APL_DATABASE_TABLE", "'.$config_afl_database_table.'");', 'define("APL_LICENSE_FILE_LOCATION", "'.$config_afl_license_file_location.'");', 'define("APL_INCLUDE_KEY_CONFIG", "'.generateRandomString(16).'");', 'define("APL_DELETE_CANCELLED", "'.$config_afl_delete_cancelled.'");', 'define("APL_DELETE_CRACKED", "'.$config_afl_delete_cracked.'");', 'define("APL_GOD_MODE", "'.$config_afl_god_mode.'");'];
                    $config_file_content = preg_replace($config_values_bad_array, $config_values_good_array, $config_file_content);

                    if (empty($config_file_content)) { //no content
                        $error_detected = 1;
                        $error_details .= 'Sample configuration file is empty.<br>';
                    } else { //everything OK
                        $action_success = 1;
                    }
                }
            } else {
                $error_detected = 1;
                $error_details .= 'Invalid product, license verification period, license storage type, license file location or MySQL table name.<br>';
            }

            if ($action_success == 1) { //everything OK
                $page_message = "$product_title configuration file generated.";
                $page_message_class = 'alert alert-success';
            } else { //display error message
                $page_message = "The configuration file could not be generated because of this reason: <br><br>$error_details";
                $page_message_class = 'alert alert-danger';
            }
            createReport(strip_tags($page_message), 1, 1, $action_success);

            //set default values for essential variables (mostly submitted to dropdown functions) when no values are set or values need to be reset
            if (! isset($product_id) || ! aflValidateIntegerValue($product_id)) {
                $product_id = 0;
            }

            if (! isset($config_afl_days) || ! aflValidateIntegerValue($config_afl_days)) {
                $config_afl_days = 7;
            }

            if (! isset($config_afl_license_file_location)) {
                $config_afl_license_file_location = 'signature/license.key.example';
            }

            if (! isset($config_afl_database_table)) {
                $config_afl_database_table = 'user_data';
            }
            $products_array = $this->returnProductsDropdownArray($product_id);

            return $config_file_content;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    //return products dropdown
    public function returnProductsDropdownArray($product_ids_array, $forced_readonly = 0)
    {
        $root_array = [];

        foreach ($rows_array = AflProducts::orderBy('product_title') as $row) {
            foreach ($row as $key => $value) {
                $item_array[$key] = $value;
            }
            $item_array['value'] = $item_array['product_id'];
            $item_array['title'] = $item_array['product_title'];
            $item_array['selected'] = $this->returnOptionStatus($item_array['value'], $product_ids_array, $this->returnReadOnlyStatus($forced_readonly, $item_array['product_status']));

            $root_array[] = $item_array;
        }

        return $root_array;
    }

    //return selection status for dropdown option
    private function returnOptionStatus($current_value, $selected_values_array, $readonly = 0)
    {
        $status = '';

        if (in_array(strval($current_value), array_map('strval', $this->convertVariableToArray($selected_values_array)))) { //convert ID(s) to array, so function works with single-select and multi-select dropdowns. also compare both values as strings to avoid false positive when current_value is some string and selected_values_array contains 0
            $status = ' selected';
        } else {
            if ($readonly == 1) { //mark non-selected option as disabled (read-only)
                $status = ' disabled';
            }
        }

        return $status;
    }

    //convert variables into arrays
    private function convertVariableToArray($var_name)
    {
        if (! is_array($var_name)) {
            $var_name = [$var_name];
        }

        return $var_name;
    }

    //return readonly status
    private function returnReadOnlyStatus($forced_readonly, $status)
    {
        $readonly = 0;

        if ($forced_readonly == 1 || isZero($status)) { //forced readonly applied or item status is inactive, make it readonly
            $readonly = 1;
        }

        return $readonly;
    }
}
