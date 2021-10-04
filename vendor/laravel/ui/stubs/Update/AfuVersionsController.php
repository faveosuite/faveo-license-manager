<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use App\Models\AflApiKeys;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflProducts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\AfuVersions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\VersionRequest;
use RecursiveIteratorIterator;

class AfuVersionsController extends Controller
{
    public function versionAdd(VersionRequest $request){

        $action_success = 0; //will be changed to 1 later only if everything OK
        $error_detected = 0; //will be changed to 1 later if error occurs
        $error_details = ""; //will be filled with errors (if any)
        $added_records = 0;
        $updated_records = 0;
        $removed_records = 0;
        $api_action_success = 0;
        $api_error_detected = 0;
        $api_error_details = "";
        $logged_admin_id = 0; //used for compatibility with createReport function in the same file in /aus_admin directory. since admin is not logged in when API is called, $logged_admin_id must be 0
        foreach($sets_array=DB::table('afl_settings')->get()->toArray() as $set){
            extract((array)$set);
        }

          foreach($rows_array = DB::table('directory')->where('id',1)->get()->toArray() as $row){
            extract((array)$row);
        }
          $path =storage_path();
        define("SCRIPT_ROOT_DIRECTORY", __DIR__);
        define("ARCHIVES_DIRECTORY", $path.$ARCHIVES_DIRECTORY);
        define("QUERIES_DIRECTORY", $path.$QUERIES_DIRECTORY);

        if (null!==(request()->server('REMOTE_ADDR')))
        {
            $ip_address=request()->server('REMOTE_ADDR');
        }
        else {
            $ip_address=$request->ip();
        }

        $product_id = $request->get('product_id');
        $api_key_secret = $request->get('api_key_secret');
        $version_number = $request->get('version_number');
        $version_status = $request->get('version_status');
        $product_status = $request->get('product_status');
        $version_install_file = $request->file('version_install_file');
        $version_install_query = $request->file('version_install_query');
        $version_raw_install_query = $request->get('version_raw_install_query');
        $version_upgrade_file=$request->file('version_upgrade_file');
        $version_upgrade_query=$request->file('version_upgrade_query');
        $version_raw_upgrade_query = $request->get('version_raw_upgrade_query');
        $version_install_limit = $request->get('version_install_limit');
        $version_upgrade_limit = $request->get('version_upgrade_limit');
        $version_changelog = $request->get('version_change_log');
        $version_expire_date = $request->get('version_expire_date');
        $version_comments = $request->get('version_comments');

        if (!empty($api_key_secret)) //prevent someone from posting to this file directly
        {
                $api = AflApiKeys::where('api_key_secret',$api_key_secret)->where('api_key_status',1)->get()->toArray();
                if(empty($api))
                {
                    return errorResponse(Lang::get('lang.invalid_api_key'),404);
                }
                else {
                    $api_ip = new AflApiKeys();
                    $api_ips = $api_ip->value('api_key_ip');

                    if (!empty($api_ips)) {
                        if (!$api_ips->contains($ip_address)) {
                            $api_error_detected = 1;
                            return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                        } else {
                            $api_action_success = 1;
                        }
                    } else {
                        $api_action_success = 1;
                    }
                }
            if ($api_action_success == 1) //API check OK, continue with actual request
            {
                /*$optional_api_parameters_array = array("version_install_file", "version_install_query", "version_raw_install_query", "version_upgrade_file", "version_upgrade_query", "version_raw_upgrade_query", "version_install_limit", "version_upgrade_limit", "version_changelog", "version_expire_date", "version_comments"); //optional API parameters for this page
                foreach ($optional_api_parameters_array as $optional_api_parameter) //in case some required parameter was not submitted, set its value empty to prevent "undefined variable" errors
                {
                    if (!isset($optional_api_parameter)) {
                        $optional_api_parameter = "";
                    }
                }*/

                if ($api_action_success=='1') //code between {} tags is identical in files with the same name in /aus_admin and /aus_api directories
                {
                    if (aflValidateIntegerValue($product_id) && !empty($version_number) && aflValidateIntegerValue($version_status, 0, 2))
                    {
                        if(!empty($version_install_file))
                        {
                        if (!empty($version_install_file->getLinkTarget()) && !validateFile($version_install_file->getLinkTarget(), $version_install_file->getClientOriginalName(), array("application/zip"), array("zip"), 104857600))
                           {
                            $error_detected =1;
                            $error_details .= "Invalid installation archive format or size (ZIP archive, 100 MB max).<br>";
                            }
                         }
                        if(!empty($version_upgrade_file))
                        {
                        if (!empty($version_upgrade_file->getLinkTarget()) && !validateFile($version_upgrade_file->getLinkTarget(),$version_upgrade_file->getClientOriginalName(), array("application/zip"), array("zip"), 104857600)) {
                            $error_detected = 1;
                            $error_details .= "Invalid upgrade archive format or size (ZIP archive, 100 MB max).<br>";
                            }
                        }
                        if(!empty($version_install_query))
                        {
                        if (!empty($version_install_query->getLinkTarget()) && !validateFile($version_install_query->getLinkTarget(), $version_install_query->getClientOriginalName(), array("application/zip"), array("zip"), 1048576)) {
                            $error_detected = 1;
                            $error_details .= "Invalid installation query format or size (ZIP archive, 1 MB max).<br>";
                        }
                       }
                        if(!empty($version_upgrade_query))
                        {
                        if (!empty($version_upgrade_query->getLinkTarget()) && !validateFile($version_upgrade_query->getLinkTarget(), $version_install_query->getClientOriginalName(), array("application/zip"), array("zip"), 1048576)) {
                            $error_detected = 1;
                            $error_details .= "Invalid upgrade query format or size (ZIP archive, 1 MB max).<br>";
                        }
                        }
                        if (!empty($version_install_limit) && !aflValidateIntegerValue($version_install_limit)) {
                            $error_detected = 1;
                            $error_details .= "Invalid version installations limit.<br>";
                        }

                        if (!empty($version_upgrade_limit) && !aflValidateIntegerValue($version_upgrade_limit)) {
                            $error_detected = 1;
                            $error_details .= "Invalid version upgrades limit.<br>";
                        }

                        if (!empty($version_expire_date) && !aflVerifyDateTime($version_expire_date, "Y-m-d")) {
                            $error_detected = 1;
                            $error_details .= "Invalid version expiration date.<br>";
                        }

                        if ($error_detected != 1) {
                            $version_date = date("Y-m-d");

                            $product_array = AflProducts::where('product_id',$product_id)->get()->toArray(); //fetch product details to be used in file names and reports
                            foreach ($product_array as $product) {
                                extract((array)$product);

                            }

                            if(!empty($version_install_file)) {
                                if (!empty($version_install_file->getLinkTarget())) //format version_install_file like product-title-version-number-installation-archive-random-string.extension
                                {
                                    $version_install_file = generateFileName(ARCHIVES_DIRECTORY, slugifyText("$product_title-$version_number-installation-archive-" . generateRandomString(8)) . "." . pathinfo($version_install_file->getClientOriginalName(), PATHINFO_EXTENSION));
                                }
                            }else {
                                $version_install_file = "";
                            }

                            if(!empty($version_upgrade_file)) {
                                if (!empty($version_upgrade_file->getLinkTarget())) //format version_upgrade_file like product-title-version-number-upgrade-archive-random-string.extension
                                {
                                    $version_upgrade_file = generateFileName(ARCHIVES_DIRECTORY, slugifyText("$product_title-$version_number-upgrade-archive-" . generateRandomString(8)) . "." . pathinfo($version_upgrade_file->getClientOriginalName(), PATHINFO_EXTENSION));
                                }
                            }else {
                                $version_upgrade_file = "";
                            }

                            if(!empty($version_install_query)) {
                                if (!empty($version_install_query->getLinkTarget())) //format version_install_query like product-title-version-number-install-query-random-string.extension
                                {
                                    $version_install_query = generateFileName(QUERIES_DIRECTORY, slugifyText("$product_title-$version_number-installation-query-" . generateRandomString(8)) . "." . pathinfo($version_install_query->getClientOriginalName(), PATHINFO_EXTENSION));
                                }
                            }else {
                                $version_install_query = "";
                            }

                            if(!empty($version_upgrade_query)) {
                                if (!empty($version_upgrade_query->getLinkTarget())) //format version_upgrade_query like product-title-version-number-upgrade-query-random-string.extension
                                {
                                    $version_upgrade_query = generateFileName(QUERIES_DIRECTORY, slugifyText("$product_title-$version_number-upgrade-query-" . generateRandomString(8)) . "." . pathinfo($version_upgrade_query->getClientOriginalName(), PATHINFO_EXTENSION));
                                }
                            }else {
                                $version_upgrade_query = "";
                            }

                            $added_records = DB::table('afu_versions')->insertOrIgnore([
                                'product_id'=>$product_id,
                                'version_number'=>$version_number,
                                'version_install_file' => $version_install_file,
                                'version_install_query' => $version_install_file,
                                'version_raw_install_query' => $version_raw_install_query,
                                'version_upgrade_file'=>$version_upgrade_file,
                                'version_upgrade_query'=> $version_upgrade_query,
                                'version_raw_upgrade_query'=>$version_raw_upgrade_query,
                                'version_install_limit'=>$version_install_limit,
                                'version_upgrade_limit'=>$version_upgrade_limit,
                                'version_changelog'=>$version_changelog,
                                'version_date'=>$version_date,
                                'version_expire_date'=>$version_expire_date,
                                'version_comments'=>$version_comments,
                                'version_status' =>$version_status
                            ]);

                            if (!aflValidateIntegerValue($added_records)) {
                                $error_detected = 1;
                                $error_details .= "Invalid record details, duplicated data, or database error.<br>";
                            } else {
                                $action_success = 1;

                                if (!empty($version_install_file)) //move uploaded version_install_file
                                {
                                    move_uploaded_file($_FILES["version_install_file"]["tmp_name"], ARCHIVES_DIRECTORY ."/$version_install_file");
                                }

                                if (!empty($version_upgrade_file)) //move uploaded version_upgrade_file
                                {
                                    move_uploaded_file($_FILES["version_upgrade_file"]["tmp_name"], ARCHIVES_DIRECTORY ."/$version_upgrade_file");
                                }

                                if (!empty($version_install_query)) //move uploaded version_install_query
                                {
                                    move_uploaded_file($_FILES["version_install_query"]["tmp_name"], QUERIES_DIRECTORY . "/$version_install_query");
                                }

                                if (!empty($version_upgrade_query)) //move uploaded version_upgrade_query
                                {
                                    move_uploaded_file($_FILES["version_upgrade_query"]["tmp_name"], QUERIES_DIRECTORY . "/$version_upgrade_query");
                                }

                                $this->disableOldVersion($product_id, $product_max_active_versions, $version_number, $version_comments); //disable a specific number of old versions if needed
                            }
                        }
                    } else {
                        $error_detected = 1;
                        $error_details .= "Invalid product, version number, or status.<br>";
                    }

                    if ($action_success == 1) //everything OK
                    {
                        $page_message = "$product_title version $version_number added.";
                        $page_message_class = "alert alert-success";
                    } else //display error message
                    {
                        $page_message = "Version could not be added because of this reason: <br><br>$error_details";
                        $page_message_class = "alert alert-danger";
                    }

                    createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);
                }
            } else //display error message
            {
                $page_message = "The action could not be completed because of this reason:<br><br>$api_error_details";
            }

            $api_response_array = array("api_action_success" => $api_action_success, "api_error_detected" => $api_error_detected, "action_success" => $action_success, "error_detected" => $error_detected, "page_message" => $page_message); //make array with response data
            return json_encode($api_response_array);
        }


    }


    public function versionUpdate(Request $request)
    {
        $action_success = 0; //will be changed to 1 later only if everything OK
        $error_detected = 0; //will be changed to 1 later if error occurs
        $error_details = ""; //will be filled with errors (if any)
        $added_records = 0;
        $updated_records = 0;
        $removed_records = 0;
        $api_action_success = 0;
        $api_error_detected = 0;
        $api_error_details = "";
        $logged_admin_id = 0; //used for compatibility with createReport function in the same file in /aus_admin directory. since admin is not logged in when API is called, $logged_admin_id must be 0

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        foreach($sets_array=DB::table('afl_settings')->get()->toArray() as $set){
            extract((array)$set);
        }

        foreach($rows_array = DB::table('directory')->where('id',1)->get()->toArray() as $row){
            extract((array)$row);
        }
        $path =storage_path();
        define("SCRIPT_ROOT_DIRECTORY", __DIR__);
        define("ARCHIVES_DIRECTORY", $path.$ARCHIVES_DIRECTORY);
        define("QUERIES_DIRECTORY", $path.$QUERIES_DIRECTORY);

        $version_id=$request->get('version_id');
        $product_id = $request->get('product_id');
        $api_key_secret = $request->get('api_key_secret');
        $version_number = $request->get('version_number');
        $version_status = $request->get('version_status');
        $version_install_file = $request->file('version_install_file');
        $version_install_query = $request->file('version_install_query');
        $version_raw_install_query = $request->get('version_raw_install_query');
        $version_upgrade_file = $request->file('version_upgrade_file');
        $version_upgrade_query = $request->file('version_upgrade_query');
        $version_raw_upgrade_query = $request->get('version_raw_upgrade_query');
        $version_install_limit = $request->get('version_install_limit');
        $version_upgrade_limit = $request->get('version_upgrade_limit');
        $version_changelog = $request->get('version_change_log');
        $version_expire_date = $request->get('version_expire_date');
        $version_comments = $request->get('version_comments');


        if (empty($version_id) || !aflValidateIntegerValue($version_id) || empty($rows_array=AfuVersions::where('version_id',$version_id)->get()->toArray())) //invalid record
        {
            return errorResponse(Lang::get('lang.invalid'),404);
        }

        if (!empty($api_key_secret)) //prevent someone from posting to this file directly
        {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get()->toArray();

            if (empty($api)) {
                return errorResponse(Lang::get('lang.invalid_api_key'), 404);
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');

                if (!empty($api_ips)) {
                    if (!$api_ips->contains($ip_address)) {
                        $api_error_detected = 1;
                        return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                    } else {
                        $api_action_success = 1;
                    }
                } else {
                    $api_action_success = 1;
                }
            }


            if ($api_action_success == 1 & $api_error_detected == 0) //code between {} tags is identical in files with the same name in /aus_admin and /aus_api directories, EXCEPT redirectInvalidRecord($script_name); line
            {
                /*if (!empty($delete_record) && $delete_record == 1) {
                    $removed_records += deleteVersion($version_id);
                    if ($removed_records > 0) {
                        $action_success = 1;

                        $page_message = "Deleted $removed_records version(s).";
                        createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);
                        echo $page_message; //THIS LINE IS CUSTOM IN API. ADMINISTRATION DASHBOARD CODE CONTAINS redirectInvalidRecord($script_name);
                        exit();
                    } else {
                        $error_detected = 1;
                        $error_details .= "Invalid record or database error.<br>";
                    }
                }*/

                if (aflValidateIntegerValue($product_id) && !empty($version_number) && aflValidateIntegerValue($version_status, 0, 2)) {
                    if(!empty($version_install_file)) {
                        if (!empty($version_install_file->getLinkTarget()) && !validateFile($version_install_file->getLinkTarget(), $version_install_file->getClientOriginalName(), array("application/zip"), array("zip"), 104857600)) {
                            $error_detected = 1;
                            $error_details .= "Invalid installation archive format or size (ZIP archive, 100 MB max).<br>";
                        }
                    }
                    if(!empty($version_upgrade_file)) {
                        if (!empty($version_upgrade_file->getLinkTarget()) && !validateFile($version_upgrade_file->getLinkTarget(), $version_upgrade_file->getClientOriginalName(), array("application/zip"), array("zip"), 104857600)) {
                            $error_detected = 1;
                            $error_details .= "Invalid upgrade archive format or size (ZIP archive, 100 MB max).<br>";
                        }
                    }
                    if(!empty($version_install_query)) {
                        if (!empty($version_install_query->getLinkTarget()) && !validateFile($version_install_query->getLinkTarget(), $version_install_query->getClientOriginalName(), array("application/zip"), array("zip"), 1048576)) {
                            $error_detected = 1;
                            $error_details .= "Invalid installation query format or size (ZIP archive, 1 MB max).<br>";
                        }
                    }
                    if(!empty($version_upgrade_query)) {
                        if (!empty($version_upgrade_query->getLinkTarget()) && !validateFile($version_upgrade_query->getLinkTarget(), $version_upgrade_query->getClientOriginalName(), array("application/zip"), array("zip"), 1048576)) {
                            $error_detected = 1;
                            $error_details .= "Invalid upgrade query format or size (ZIP archive, 1 MB max).<br>";
                        }
                    }
                    if (!empty($version_install_limit) && !aflValidateIntegerValue($version_install_limit)) {
                        $error_detected = 1;
                        $error_details .= "Invalid version installations limit.<br>";
                    }

                    if (!empty($version_upgrade_limit) && !aflValidateIntegerValue($version_upgrade_limit)) {
                        $error_detected = 1;
                        $error_details .= "Invalid version upgrades limit.<br>";
                    }

                    if (!empty($version_expire_date) && !aflVerifyDateTime($version_expire_date, "Y-m-d")) {
                        $error_detected = 1;
                        $error_details .= "Invalid version expiration date.<br>";
                    }

                    if ($error_detected != 1) {
                        $product_array = AflProducts::where('product_id', $product_id)->get()->toArray();//fetchRow("SELECT * FROM aus_products WHERE product_id=?", array($product_id), array("i")); //fetch product details to be used in file names and reports
                        foreach ($product_array as $product) {
                            extract((array)$product);

                        }
                        if (!empty($version_install_file)) {
                            if (!empty($version_install_file->getLinkTarget())) //format version_install_file like product-title-version-number-installation-archive-random-string.extension
                            {
                                $version_install_file = generateFileName(ARCHIVES_DIRECTORY, slugifyText("$product_title-$version_number-installation-archive-" . generateRandomString(8)) . "." . pathinfo($version_install_file->getClientOriginalName(), PATHINFO_EXTENSION));
                            }
                        } else {
                            $version_install_file = "";
                        }
                        if (!empty($version_upgrade_file)) {
                            if (!empty($version_upgrade_file->getLinkTarget())) //format version_upgrade_file like product-title-version-number-upgrade-archive-random-string.extension
                            {
                                $version_upgrade_file = generateFileName(ARCHIVES_DIRECTORY, slugifyText("$product_title-$version_number-upgrade-archive-" . generateRandomString(8)) . "." . pathinfo($version_upgrade_file->getClientOriginalName(), PATHINFO_EXTENSION));
                            }
                        } else {
                            $version_upgrade_file = "";
                        }
                        if (!empty($version_install_query)){
                            if (!empty($version_install_query->getLinkTarget())) //format version_install_query like product-title-version-number-install-query-random-string.extension
                            {
                                $version_install_query = generateFileName(QUERIES_DIRECTORY, slugifyText("$product_title-$version_number-installation-query-" . generateRandomString(8)) . "." . pathinfo($version_install_query->getClientOriginalName(), PATHINFO_EXTENSION));
                            }
                          }else {
                            $version_install_query = "";
                        }

                        if(!empty($version_upgrade_query)) {
                            if (!empty($version_upgrade_query->getLinkTarget())) //format version_upgrade_query like product-title-version-number-upgrade-query-random-string.extension
                            {
                                $version_upgrade_query = generateFileName(QUERIES_DIRECTORY, slugifyText("$product_title-$version_number-upgrade-query-" . generateRandomString(8)) . "." . pathinfo($version_upgrade_query->getClientOriginalName(), PATHINFO_EXTENSION));
                            }
                        }else {
                            $version_upgrade_query = "";
                        }

                        if (empty($version_install_file)) {
                            $version_install_file = $rows_array[0]['version_install_file']; //use old value when no new version_install_file uploaded
                        }

                        if (empty($version_upgrade_file)) {
                            $version_upgrade_file = $rows_array[0]['version_upgrade_file']; //use old value when no new version_upgrade_file uploaded
                        }

                        if (empty($version_install_query)) {
                            $version_install_query = $rows_array[0]['version_install_query']; //use old value when no new version_install_query uploaded
                        }

                        if (empty($version_upgrade_query)) {
                            $version_upgrade_query = $rows_array[0]['version_upgrade_query']; //use old value when no new version_upgrade_query uploaded
                        }

                        if (!empty($delete_version_install_file) && $delete_version_install_file == 1) {
                            $this->deleteFileDirectory(ARCHIVES_DIRECTORY, array($rows_array[0]['version_install_file'])); //delete old version_install_file (if any)
                            $version_install_file = "";
                        }

                        if (!empty($delete_version_upgrade_file) && $delete_version_upgrade_file == 1) {
                            $this->deleteFileDirectory(ARCHIVES_DIRECTORY, array($rows_array[0]['version_upgrade_file'])); //delete old version_upgrade_file (if any)
                            $version_upgrade_file = "";
                        }

                        if (!empty($delete_version_install_query) && $delete_version_install_query == 1) {
                            $this->deleteFileDirectory(QUERIES_DIRECTORY, array($rows_array[0]['version_install_query'])); //delete old version_install_query (if any)
                            $version_install_query = "";
                        }

                        if (!empty($delete_version_upgrade_query) && $delete_version_upgrade_query == 1) {
                            $this->deleteFileDirectory(QUERIES_DIRECTORY, array($rows_array[0]['version_upgrade_query'])); //delete old version_upgrade_query (if any)
                            $version_upgrade_query = "";
                        }

                        if (!empty($reset_install_count) && $reset_install_count == 1) {
                            $version_install_count = "";
                        } else {
                            $version_install_count = $rows_array[0]['version_install_count']; //use old value when no reset is needed
                        }

                        if (!empty($reset_upgrade_count) && $reset_upgrade_count == 1) {
                            $version_upgrade_count = "";
                        } else {
                            $version_upgrade_count = $rows_array[0]['version_upgrade_count']; //use old value when no reset is needed
                        }

                        $updated_records += DB::table('afu_versions')->where('version_id', $version_id)
                            ->update([
                                'version_install_file' => $version_install_file,
                                'version_install_query' => $version_install_query,
                                'version_raw_install_query' => $version_raw_install_query,
                                'version_upgrade_file' => $version_upgrade_file,
                                'version_upgrade_query' => $version_upgrade_query,
                                'version_raw_upgrade_query' => $version_raw_upgrade_query,
                                'version_install_limit' => $version_install_limit,
                                'version_install_count' => $version_install_count,
                                'version_upgrade_limit' => $version_upgrade_limit,
                                'version_upgrade_count' => $version_upgrade_count,
                                'version_changelog' => $version_changelog,
                                'version_expire_date' => $version_expire_date,
                                'version_comments' => $version_comments,
                                'version_status' => $version_status
                            ]);
                        //doMysqlQuery("UPDATE aus_versions SET version_install_file=?, version_install_query=?, version_raw_install_query=?, version_upgrade_file=?, version_upgrade_query=?, version_raw_upgrade_query=?, version_install_limit=?, version_install_count=?, version_upgrade_limit=?, version_upgrade_count=?, version_changelog=?, version_expire_date=?, version_comments=?, version_status=? WHERE version_id=?", array($version_install_file, $version_install_query, $version_raw_install_query, $version_upgrade_file, $version_upgrade_query, $version_raw_upgrade_query, $version_install_limit, $version_install_count, $version_upgrade_limit, $version_upgrade_count, $version_changelog, $version_expire_date, $version_comments, $version_status, $version_id), array("s", "s", "s", "s", "s", "s", "i", "i", "i", "i", "s", "s", "s", "i", "i"));
                        if (!aflValidateIntegerValue($updated_records)) {
                            $error_detected = 1;
                            $error_details .= "Invalid record details, duplicated data, or database error.<br>";
                        } else {
                            $action_success = 1;

                            if(!empty($version_install_file)) {
                                if (!empty($version_install_file->getLinkTarget())) //move uploaded version_install_file
                                {
                                    move_uploaded_file($version_install_file->getLinkTarget(), ARCHIVES_DIRECTORY . "/$version_install_file");
                                    $this->deleteFileDirectory(ARCHIVES_DIRECTORY, array($rows_array[0]['version_install_file'])); //delete old version_install_file (if any)
                                }
                            }
                            if(!empty($version_upgrade_file)) {
                                if (!empty($_FILES["version_upgrade_file"]["tmp_name"])) //move uploaded version_upgrade_file
                                {
                                    move_uploaded_file($_FILES["version_upgrade_file"]["tmp_name"], ARCHIVES_DIRECTORY . "/$version_upgrade_file");
                                    $this->deleteFileDirectory(ARCHIVES_DIRECTORY, array($rows_array[0]['version_upgrade_file'])); //delete old version_upgrade_file (if any)
                                }
                            }
                            if(!empty($version_install_query)) {
                                if (!empty($version_install_query->getLinkTarget())) //move uploaded version_install_query
                                {
                                    move_uploaded_file($version_install_query->getLinkTarget(), QUERIES_DIRECTORY . "/$version_install_query");
                                    $this->deleteFileDirectory(QUERIES_DIRECTORY, array($rows_array[0]['version_install_query'])); //delete old version_install_query (if any)
                                }
                            }
                            if(!empty($version_upgrade_query)) {
                                if (!empty($version_upgrade_query->getLinkTarget())) //move uploaded version_upgrade_query
                                {
                                    move_uploaded_file($version_upgrade_query->getLinkTarget(), QUERIES_DIRECTORY . "/$version_upgrade_query");
                                    $this->deleteFileDirectory(QUERIES_DIRECTORY, array($rows_array[0]['version_upgrade_query'])); //delete old version_upgrade_query (if any)
                                }
                            }
                        }
                    }
                } else {
                    $error_detected = 1;
                    $error_details .= "Invalid product, version number or status.<br>";
                }

                if ($action_success == 1) //everything OK
                {
                    $page_message = "$product_title version $version_number updated.";
                    $page_message_class = "alert alert-success";
                } else //display error message
                {
                    $page_message = "Version could not be updated because of this reason: <br><br>$error_details";
                    $page_message_class = "alert alert-danger";
                }

                createReport(strip_tags($page_message), $logged_admin_id, 1, $action_success);
            } else //display error message
            {
                $page_message = "The action could not be completed because of this reason:<br><br>$api_error_details";
            }

            $api_response_array = array("api_action_success" => $api_action_success, "api_error_detected" => $api_error_detected, "action_success" => $action_success, "error_detected" => $error_detected, "page_message" => $page_message); //make array with response data
            return json_encode($api_response_array);

        }
        }




    //delete version
    public function deleteVersion(Request $request)
    {
    $removed_records=0;
    $version_id = $request->get('version_id');
    $api_key_secret = $request->get('api_key_secret');


        if (null!==(request()->server('REMOTE_ADDR')))
        {
            $ip_address=request()->server('REMOTE_ADDR');
        }
        else {
            $ip_address=$request->ip();
        }
        if (!empty($api_key_secret)) //prevent someone from posting to this file directly
        {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get()->toArray();
            if (empty($api)) {
                return errorResponse(Lang::get('lang.invalid_api_key'), 404);
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');

                if (!empty($api_ips)) {
                    if (!$api_ips->contains($ip_address)) {
                        $api_error_detected = 1;
                        return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                    } else {
                        $api_action_success = 1;
                    }
                } else {
                    $api_action_success = 1;
                }
            }

            if (aflValidateIntegerValue($version_id) && $api_action_success==1) {
                if (!empty($rows_array = AfuVersions::where('version_id', $version_id)->get()->toArray())) //get version_install_file, version_install_query, version_upgrade_file, version_upgrade_query (if any) to remove from server
                {
                    foreach ($rows_array as $row)
                    {
                        extract((array)$row);
                        try {
                            DB::beginTransaction();
                            $transaction_errors_array = array();

                            AflCallbacks::where('version_id', $version_id)->delete();//doMysqlQuery("DELETE FROM aus_callbacks WHERE version_id=?", array($version_id), array("i")); //delete callback

                            AflInstallations::where('version_id', $version_id)->delete();//doMysqlQuery("DELETE FROM aus_installations WHERE version_id=?", array($version_id), array("i")); //delete installation

                            $removed_records += AfuVersions::where('version_id', $version_id)->delete();//doMysqlQuery("DELETE FROM aus_versions WHERE version_id=?", array($version_id), array("i"));

                            DB::commit();
                        } catch (Exception $e) {
                            $transaction_errors_array[] = $e->getMessage();

                        }
                        if (!empty(array_filter($transaction_errors_array))) //one of queries failed, revert whole transaction
                        {
                            DB::rollBack();
                            $removed_records = 0;
                            return errorResponse(Lang::get('lang.invalid'), 404);
                        } else //everything ok, delete obsolete files
                        {
                            $this->deleteFileDirectory(ARCHIVES_DIRECTORY, array($version_install_file, $version_upgrade_file)); //remove version_install_file and version_upgrade_file (if any) from server
                            $this->deleteFileDirectory(QUERIES_DIRECTORY, array($version_install_query, $version_upgrade_query)); //remove version_install_query and version_upgrade_query (if any) from server
                            return successResponse(Lang::get('lang.deleted'),$removed_records,200);
                        }
                    }
                }
            }

            return errorResponse(Lang::get('lang.not_found'), 404);
        }
    }



//set old versions as expired when new version is added
public function disableOldVersion($product_id, $product_max_active_versions, $version_number, $version_comments)
    {
    if (aflValidateIntegerValue($product_id) && aflValidateIntegerValue($product_max_active_versions) && !empty($version_number))
        {
        $version_expire_date=date("Y-m-d");
        if (empty($version_comments))
            {
            $version_comments="$product_max_active_versions active versions supported - expired on $version_expire_date after adding version $version_number";
            }
        else
            {
            $version_comments.=" ($product_max_active_versions active versions supported - expired on $version_expire_date after adding version $version_number)";
            }


        $versionId=DB::select(
            "(SELECT version_id
              FROM (SELECT version_id
                  FROM afu_versions
                  WHERE product_id=? ORDER BY version_id DESC LIMIT ?) temp_table)",
                  array($product_id,$product_max_active_versions));

            $versionId= json_decode( json_encode($versionId), true);

            DB::table('afu_versions')
                  ->whereNotIn('version_id',$versionId)
                  ->where('product_id',$product_id)
                  ->update(['version_expire_date'=> $version_expire_date,'version_comments'=>$version_comments]);

        //doMysqlQuery("UPDATE aus_versions SET version_expire_date=?, version_comments=? WHERE product_id=? AND version_id NOT IN (SELECT version_id FROM (SELECT version_id FROM aus_versions WHERE product_id=? ORDER BY version_id DESC LIMIT ?) temp_table)", array($version_expire_date, $version_comments, $product_id, $product_id, $product_max_active_versions), array("s", "s", "i", "i", "i")); //use sub-query because new MySQL doesn't support this type of query - https://stackoverflow.com/questions/19344004/1235-this-version-of-mysql-doesnt-yet-support-limit-in-all-any-some-subqu
        }
    }


    //delete files and directories from specified directory ($files_array is an array of files and/or sub-directories to be deleted from $root_directory)
    public function deleteFileDirectory($root_directory=__DIR__, $files_array=array())
    {
        $removed_records=0;

        if (is_dir($root_directory)) //specified directory exists
        {
            if (empty($files_array)) //get and delete all files from specified directory
            {
                $files_array=scandir($root_directory);
            }

            $files_array=array_filter($files_array); //remove empty files (if any) from $files_array to prevent parent directory from being deleted too
            $files_array=array_diff($files_array, array(".", "..", "")); //remove dot files (if any) from $files_array to prevent parent directory from being deleted too when $files_array contains "."
            $files_array=array_values($files_array); //re-index array to prevent errors of undefined array indices

            if (!empty($files_array)) //proceed deleting files/directories
            {
                foreach ($files_array as $file)
                {
                    if (is_file("$root_directory/$file") && unlink("$root_directory/$file")) //this is a file, delete
                    {
                        $removed_records++;
                    }

                    if (is_dir("$root_directory/$file")) //this is a directory, enter it and delete all files inside first
                    {
                        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$root_directory/$file", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
                        {
                            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
                        }

                        if (rmdir("$root_directory/$file"))
                        {
                            $removed_records++;
                        }
                    }
                }
            }
        }
        return $removed_records;
    }
}
