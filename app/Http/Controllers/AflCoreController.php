<?php

namespace App\Http\Controllers\AflCoreFunctions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Consist of functionalities for the Installing the license in Auto Faveo licenser
 * Class AflInstallLicenseController
 */
class AflInstallLicenseController extends Controller
{
    /**
     * To install the license
     *
     * @param $ROOT_URL
     * @param $CLIENT_EMAIL
     * @param $LICENSE_CODE
     * @return notifications_array if the install was successful
     */
    public function aflInstallLicense(Request $request)
    {
        $notifications_array = [];

        $ROOT_URL = $request->get('ROOT_URL');
        $CLIENT_EMAIL = $request->get('CLIENT_EMAIL');
        $LICENSE_CODE = $request->input('LICENSE_CODE');

        if (empty($apl_core_notifications = aflCheckSettings())) {//only continue if script is properly configured
            if (! empty($this->aflGetLicenseData()) && is_array($this->aflGetLicenseData())) { //license already installed
                $notifications_array['notification_case'] = 'notification_already_installed';
                $notifications_array['notification_text'] = \config('constants.Basic.AFL_NOTIFICATION_SCRIPT_ALREADY_INSTALLED');
            } else { //license not yet installed, do it now
                if (empty($apl_user_input_notifications = $this->aflCheckUserInput($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE))) { //data submitted by user is valid
                    $INSTALLATION_HASH = Hash::make($ROOT_URL.$CLIENT_EMAIL.$LICENSE_CODE); //generate hash
                    $post_info = 'product_id='.rawurlencode(config('constants.Basic.AFL_PRODUCT_ID')).'&client_email='.rawurlencode($CLIENT_EMAIL).'&license_code='.rawurlencode($LICENSE_CODE).'&root_url='.rawurlencode($ROOT_URL).'&installation_hash='.rawurlencode($INSTALLATION_HASH).'&license_signature='.rawurlencode(aflGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));
                    $content_array = aflCustomPost(config('constants.Basic.AFL_ROOT_URL').'/api/licenseinstall', $post_info, $ROOT_URL);

                    $notifications_array = aflParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);
                //$notifications_array['notification_case']="notification_license_ok";
                    if ($notifications_array['notification_case'] == 'notification_license_ok') { //everything OK
                        /*password_hash(date("Y-m-d"), PASSWORD_DEFAULT)*/
                        $INSTALLATION_KEY = aflCustomEncrypt(bcrypt(date('Y-m-d')), config('constants.Basic.AFL_SALT').$ROOT_URL); //generate $INSTALLATION_KEY first because it will be used as salt to encrypt LCD and LRD!!!
                        $LCD = aflCustomEncrypt(date('Y-m-d', strtotime('-'.config('constants.Basic.AFL_DAYS').' days')), config('constants.Basic.AFL_SALT').$INSTALLATION_KEY); //license will need to be verified right after installation
                        $LRD = aflCustomEncrypt(date('Y-m-d'), config('constants.Basic.AFL_DAYS').$INSTALLATION_KEY);

                        if (\config('constants.Basic.AFL_STORAGE') == 'DATABASE') { //license stored in database
                            $content_array = aflCustomPost(config('constants.Basic.AFL_ROOT_URL').'/api/licensescheme', $post_info, $ROOT_URL);

                        //get license scheme (use the same $post_info from license installation)
                            $notifications_array = aflParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE); //process response from Auto Faveo Licenser server
                            if (! empty($notifications_array['notification_data']) && ! empty($notifications_array['notification_data']['scheme_query'])) { //valid scheme received
                                $mysql_bad_array = ["%config('constants.Basic.AFL_DATABASE_TABLE')%", '%ROOT_URL%', '%CLIENT_EMAIL%', '%LICENSE_CODE%', '%LCD%', '%LRD%', '%INSTALLATION_KEY%', '%INSTALLATION_HASH%'];
                                $mysql_good_array = [config('constants.Basic.AFL_DATABASE_TABLE'), $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $LCD, $LRD, $INSTALLATION_KEY, $INSTALLATION_HASH];
                                $license_scheme = str_replace($mysql_bad_array, $mysql_good_array, $notifications_array['notification_data']['scheme_query']); //replace some variables with actual values

                                //  mysqli_multi_query($MYSQLI_LINK, $license_scheme) or die(mysqli_error($MYSQLI_LINK));
                                try {
                                    $table = config('constants.Basic.AFL_DATABASE_TABLE');
                                    DB::table($table)->insertOrIgnore([
                                        'ROOT_URL' => $ROOT_URL,
                                        'CLIENT_EMAIL' => $CLIENT_EMAIL,
                                        'LICENSE_CODE' => $LICENSE_CODE,
                                        'LCD' => $LCD,
                                        'LRD' => $LRD,
                                        'INSTALLATION_KEY' => $INSTALLATION_KEY,
                                        'INSTALLATION_HASH' => $INSTALLATION_HASH,
                                    ]);
                                } catch (Exception $e) {
                                    return $e->getMessage();
                                }
                            }
                        }

                        if (config('constants.BASIC.AFL_STORAGE') == 'FILE') { //license stored in file
                            $handle = @fopen(config('constants.Extra.AFL_DIRECTORY').'/'.\config('constants.Basic.AFL_LICENSE_FILE_LOCATION'), 'w+');
                            $fwrite = @fwrite($handle, "<ROOT_URL>$ROOT_URL</ROOT_URL><CLIENT_EMAIL>$CLIENT_EMAIL</CLIENT_EMAIL><LICENSE_CODE>$LICENSE_CODE</LICENSE_CODE><LCD>$LCD</LCD><LRD>$LRD</LRD><INSTALLATION_KEY>$INSTALLATION_KEY</INSTALLATION_KEY><INSTALLATION_HASH>$INSTALLATION_HASH</INSTALLATION_HASH>");
                            if ($fwrite === false) { //updating file failed
                                echo config('constants.Basic.AFL_NOTIFICATION_LICENSE_FILE_WRITE_ERROR');
                                exit();
                            }
                            @fclose($handle);
                        }
                    }
                } else { //data submitted by user is invalid
                    $notifications_array['notification_case'] = 'notification_user_input_invalid';
                    $notifications_array['notification_text'] = implode('; ', $apl_user_input_notifications);
                }
            }
        } else { //script is not properly configured
            $notifications_array['notification_case'] = 'notification_script_corrupted';
            $notifications_array['notification_text'] = implode('; ', $apl_core_notifications);
        }

        return $notifications_array;
    }

    /**
     * checks user input
     *
     * @param $ROOT_URL
     * @param $CLIENT_EMAIL
     * @param $LICENSE_CODE
     * @return notifications_array with error messages if something wrong
     */
    protected function aflCheckUserInput($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)
    {
        $notifications_array = [];

        if (empty($ROOT_URL) || ! filter_var($ROOT_URL, FILTER_VALIDATE_URL) || ! ctype_alnum(substr($ROOT_URL, -1))) { //invalid installation url
            $notifications_array[] = config('constants.NFU.AFL_USER_INPUT_NOTIFICATION_INVALID_ROOT_URL');
        }

        if (empty($CLIENT_EMAIL) && empty($LICENSE_CODE)) { //both email and code empty
            $notifications_array[] = config('constants.NFU.AFL_USER_INPUT_NOTIFICATION_EMPTY_LICENSE_DATA');
        }

        if (! empty($CLIENT_EMAIL) && ! filter_var($CLIENT_EMAIL, FILTER_VALIDATE_EMAIL)) { //invalid email
            $notifications_array[] = config('constants.NFU.AFL_USER_INPUT_NOTIFICATION_INVALID_EMAIL');
        }

        if (! empty($LICENSE_CODE) && ! is_string($LICENSE_CODE)) { //invalid license code
            $notifications_array[] = config('constants.NFU.AFL_USER_INPUT_NOTIFICATION_INVALID_LICENSE_CODE');
        }

        return $notifications_array;
    }

    /**
     * retrives the license data wheather it's stored in a database or file
     *
     * @return setting_row array consisting of license data
     */
    public function aflGetLicenseData()
    {
        $settings_row = [];

        if (config('constants.Basic.AFL_STORAGE') == 'DATABASE') { //license stored in database (use @ before mysqli_ function to prevent errors when function is executed by aplInstallLicense function)
            $settings_results = config('constants.Basic.AFL_DATABASE_TABLE');
            $settings_row = DB::table($settings_results)->get()->toArray();
        }

        if (config('constants.Basic.AFL_STORAGE') == 'FILE') { //license stored in file
            $settings_row = aflParseLicenseFile();
        }

        return $settings_row;
    }
}
