<?php


namespace App\Traits;

namespace App\Models;
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
trait Settings
{

public function settings(){

error_reporting(E_ALL);

//enhance security
if (substr(php_sapi_name(), 0, 3)!="cli") //don't send headers when script running in cli or cli-server mode
    {
    header("Strict-Transport-Security:max-age=31536000; includeSubDomains"); //enable HTTP Strict Transport Security
    header("X-Frame-Options: sameorigin"); //only allow frames from same origin
    header("X-XSS-Protection: 1; mode=block"); //enable XSS filtering
    header("X-Content-Type-Options: nosniff"); //opt-out of MIME type sniffing
    header("Referrer-Policy: same-origin"); //only set referrer on requests to the same origin
    header("Cache-Control: no-cache, no-store; must-revalidate; max-age=0"); //prevent caching
    }

//set script directory
define("SCRIPT_ROOT_DIRECTORY", __DIR__);

//get IP, refer, requested page, script filename, and user agent
if (null!==(\request()->server('REMOTE_ADDR'))) {$ip_address=request()->server('REMOTE_ADDR');} else {$ip_address="";}
if (null!==(\request()->server('HTTP_REFERER'))) {$refer=request()->server('HTTP_REFERER');} else {$refer="";}
if (null!==(\request()->server('REQUEST_URI'))) {$requested_url=request()->server('REQUEST_URI');} else {$requested_url="";}
if (null!==(\request()->server('SCRIPT_FILENAME'))) {$script_name=basename(request()->server('SCRIPT_FILENAME'));} else {$script_name="";}
if (null!==(\request()->server('HTTP_USER_AGENT'))) {$user_agent=request()->server('HTTP_USER_AGENT');} else {$user_agent="";}

//load extra libraries
/*require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/phpmillion_core.php");
require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/phpmillion_modules.php");
require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/phpmillion_plugins.php");
require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/phpmillion_plugins_gui.php");
require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/phpmillion_queries.php");
require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/apl_core_configuration.php");
require_once(SCRIPT_ROOT_DIRECTORY."/apl_modules/apl_core_functions.php");
require_once(SCRIPT_ROOT_DIRECTORY."/lib/swiftmailer/swift_required.php");
require_once(SCRIPT_ROOT_DIRECTORY."/lib/Twig/Autoloader.php");
require_once(SCRIPT_ROOT_DIRECTORY."/lib/html-compress-twig/autoload.php");*/

establishMysqlConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

//get script settings
foreach ($rows_array=DB::table('afl_settings')->get() as $row)
    {
    extract($row);
    }

//block banned hosts
blockBannedHosts($BANNED_HOSTS, $BANNED_HOST_MESSAGE, $FAILED_HOSTS_FORGET, $ip_address);

//set timezone
date_default_timezone_set($TIMEZONE);

//set cookie prefix
define("COOKIE_PREFIX", "afl");

//set GUI languages
$GUI_LANGUAGES_ARRAY=array("English"=>"en");

//set files to be excluded from refer check when filtering submitted data
$FILES_TO_EXCLUDE_REFER_CHECK_ARRAY=array("apl_api/api.php", "apl_callbacks/connection_test.php", "apl_callbacks/license_install.php", "apl_callbacks/license_scheme.php" ,"apl_callbacks/license_support.php" ,"apl_callbacks/license_uninstall.php" , "apl_callbacks/license_update.php", "apl_callbacks/license_updates.php", "apl_callbacks/license_verify.php", "apl_callbacks/mysql_install.php", "apl_callbacks/software_download.php", "apl_callbacks/verify_envato_purchase.php", "apl_callbacks/version_check.php");

//set anonymous files (user will not be forced to login when requesting one of these pages)
$ANONYMOUS_FILES_ARRAY=array("login.php", "login_lostpassword.php", "login_resetpassword.php", "logout.php");

//set form fields that allow HTML-like tags (where key is script filename and value is a sub-array with names of form fields that allow HTML)
$FORM_FIELDS_WITH_TAGS=array("emails_edit.php"=>array("email_expiring_license_text", "email_expiring_updates_text", "email_expiring_support_text"), "login_resetpassword.php"=>array("login_password_1", "login_password_2"), "login.php"=>array("login_password"), "profile_edit.php"=>array("login_password_1", "login_password_2"));

//set supported browsers (internal requests only coming from these browsers will be processed)
$SUPPORTED_BROWSERS_ARRAY=array("Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0", "phpmillion Custom Post", "phpmillion cURL");

//set supported API functions
$SUPPORTED_API_FUNCTIONS_ARRAY=array("banned_hosts_add", "banned_hosts_edit", "clients_add", "clients_edit", "installations_edit", "licenses_add", "licenses_edit", "products_add", "products_edit", "search");

//set supported API search types
$SUPPORTED_API_SEARCHES_ARRAY=array("banned_host", "callback", "client", "installation", "license", "product", "report");

//verify license

$license_notifications_array=aflVerifyLicense($GLOBALS["mysqli"]);
if ($license_notifications_array['notification_case']!="notification_license_ok")
    {
    echo $license_notifications_array['notification_text'];
    exit();
    }

//load auto tasks
cleanupDatabase($DATABASE_CLEANUP_ENABLED, $DATABASE_CLEANUP_CALLBACKS, $DATABASE_CLEANUP_REPORTS_MAIN, $DATABASE_CLEANUP_REPORTS_SYSTEM, $DATABASE_CLEANUP_LICENSES, $DATABASE_CLEANUP_DATE); //cleanup database
sendReminderEmails($PRODUCT_NAME, $EMAIL_FROM_NAME, $EMAIL_FROM_ADDRESS, $EMAIL_CC_SENDER, $EMAIL_EXPIRING_LICENSE_DAYS, $EMAIL_EXPIRING_UPDATES_DAYS, $EMAIL_EXPIRING_SUPPORT_DAYS, $EXPIRATION_CHECK_DATE); //send reminder emails

$setting = $request->post();
if (!empty($setting)) //filter and unset raw $_POST data if basic verification fails
    {
    $setting=filterRawPostData($FILES_TO_EXCLUDE_REFER_CHECK_ARRAY, $ROOT_URL, $setting, $refer, basename(dirname($requested_url))."/".$script_name);
    }
}
}
