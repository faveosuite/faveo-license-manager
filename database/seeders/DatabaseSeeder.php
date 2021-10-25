<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AflNotifications;
use App\Models\AflSettings;
use App\Models\AflLicenseSchemes;
use App\Models\AflApiKeys;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->api();
      $this->schemes();
      $this->notifications();
      $this->settings();
      $this->oAuthClients();
      $this->personalClients();
    }

    public function api()
    {
        AflApiKeys::updateOrCreate([
         'api_key_secret' => '5hDuaXuTh9gTLfPL',
         'api_key_ip' =>'',
         'api_key_clients_add' => 1,
         'api_key_clients_edit' => 1,
         'api_key_licenses_add' => 1,
         'api_key_licenses_edit' => 1,
         'api_key_products_add' => 1,
         'api_key_products_edit' => 1,
         'api_key_installations_edit'=>1,
         'api_key_search' =>1,
         'api_key_status'=>1
       ]);
    }

    public function schemes()
    {

       AflLicenseSchemes::updateOrCreate([
            'scheme_query' => "CREATE TABLE %APL_DATABASE_TABLE% (SETTING_ID TINYINT(1) NOT NULL AUTO_INCREMENT,ROOT_URL VARCHAR(250) NOT NULL,CLIENT_EMAIL VARCHAR(250) NOT NULL,LICENSE_CODE VARCHAR(250) NOT NULL,LCD VARCHAR(250) NOT NULL,LRD VARCHAR(250) NOT NULL,INSTALLATION_KEY VARCHAR(250) NOT NULL,INSTALLATION_HASH VARCHAR(250) NOT NULL,PRIMARY KEY (SETTING_ID)) DEFAULT CHARSET=utf8;INSERT INTO %APL_DATABASE_TABLE% (SETTING_ID, ROOT_URL, CLIENT_EMAIL, LICENSE_CODE, LCD, LRD, INSTALLATION_KEY, INSTALLATION_HASH) VALUES ('1', '%ROOT_URL%', '%CLIENT_EMAIL%', '%LICENSE_CODE%', '%LCD%', '%LRD%', '%INSTALLATION_KEY%', '%INSTALLATION_HASH%');",
            'scheme_status' =>1
       ]);
    }

    public function notifications()
    {

            AflNotifications::updateOrCreate([
            'notification_product_not_found' => 'Requested product not found',
            'notification_product_inactive' =>  'Product %PRODUCT_TITLE% is inactive',
            'notification_license_ok' =>        'License OK',
            'notification_license_not_found' =>  'License with license code %LICENSE_CODE% not found (or product not found or is inactive)',
            'notification_invalid_ip' =>  '%PRODUCT_TITLE% installation on IP address %IP_ADDRESS% is not allowed',
            'notification_invalid_domain' =>  '%PRODUCT_TITLE% installation on domain %ROOT_URL% is not allowed',
            'notification_domain_required' =>  '%PRODUCT_TITLE% installation is only allowed on a real and working domain',
            'notification_domain_in_use' => 'Domain %ROOT_URL% is already in use by another client',
            'notification_license_suspended' => '%PRODUCT_TITLE% license suspended',
            'notification_license_expired' =>  '%PRODUCT_TITLE% license expired on %LICENSE_EXPIRE_DATE% ,Please renew it on  billing.faveohelpdesk.com',
            'notification_updates_expired' => '%PRODUCT_TITLE% updates expired on %LICENSE_UPDATES_DATE% ',
            'notification_support_expired'=>  '%PRODUCT_TITLE% support expired on %LICENSE_SUPPORT_DATE%',
            'notification_license_cancelled' => '%PRODUCT_TITLE% license cancelled on %LICENSE_CANCEL_DATE%',
            'notification_license_limit' => 'The maximum number of allowed  %PRODUCT_TITLE% installations (%LICENSE_LIMIT% installation(s) total) reached',
            'notification_installation_not_found'=> '%PRODUCT_TITLE% installation on domain %ROOT_URL% and/or IP address %IP_ADDRESS% not found',
            'notification_invalid_signature' => 'License signature is invalid',
            'notification_host_banned' => 'Hostname %IP_ADDRESS% is banned',
            'notification_unknown_error' => 'An unknown error occurred (probably database failure or unauthorized modification of data)'

       ]);
    }

    public function settings()
    {

         AflSettings::updateOrCreate([
        'ROOT_URL'=>'https://license.faveohelpdesk.com',
        'CLIENT_EMAIL' => 'admin@ladybirdweb.com',
        'LICENSE_CODE' => 'a607e11e-5949-4339-a182-e49f2bd51186',
        'INSTALLATION_HASH' => '0b656b2163477d0aa1fa698d014fba16dd03703273b2c205ca14576560a13d89',
        'SYSTEM_LANGUAGE' => 'en',
        'TIMEZONE' => 'UTC',
        'RECORDS_ARCHIVE_DAYS' => '365',
        'RECORDS_ON_ADMIN_PAGE' => '10',
        'RECORDS_ON_INDEX_PAGE' => '5',
        'RECORDS_ON_SEARCH_PAGE'=> '10',
        'API_STATUS' => '1',
        'BANNED_HOSTS' => '0',
        'BANNED_HOST_MESSAGE' => '',
        'FAILED_LOGINS_LIMIT' => '0',
        'FAILED_LICENSINGS_LIMIT' =>'0',
        'FAILED_HOSTS_FORGET' =>'1',
        'SMART_REPORTS' =>'1',
        'SMART_TABLES' =>'1',
        'MIN_PASSWORD_LENGTH'=>'8',
        'WHITELISTED_ACCESS'=>'0',
        'WHITELISTED_IP' =>'',
        'EMAIL_FROM_NAME' =>'Faveo Helpdesk',
        'EMAIL_FROM_ADDRESS' => 'support@faveohelpdesk.com',
        'EMAIL_CC_ADMIN' => '0',
        'EMAIL_EXPIRING_LICENSE_DAYS' =>'0',
        'EMAIL_EXPIRING_UPDATES_DAYS' =>'0',
        'EMAIL_EXPIRING_SUPPORT_DAYS' =>'0',
        'EXPIRATION_CHECK_DATE' =>'2021-08-04',
        'DATABASE_CLEANUP_ENABLED'=>'1',
        'DATABASE_CLEANUP_CALLBACKS' =>'90',
        'DATABASE_CLEANUP_REPORTS_MAIN' =>'90',
        'DATABASE_CLEANUP_REPORTS_SYSTEM'=>'60',
        'DATABASE_CLEANUP_REPORTS_LICENSES'=>'0',
        'DATABASE_CLEANUP_DATE'=> '2021-08-04',
        'NEWS_TEXT'=> '[{"title":"phpmillion Scripts Receive New Features and Core Updates","full_url":"https:\/\/www.phpmillion.com\/blog\/phpmillion-scripts-receive-new-features-and-core-updates\/","date":"2019-08-19"},{"title":"Force User Input Validation with Auto PHP Licenser 2.5","full_url":"https:\/\/www.phpmillion.com\/blog\/force-user-input-validation-with-auto-php-licenser-2-5\/","date":"2019-04-18"},{"title":"Dead Man Switch 1.4 Gets Visual Emails Composer","full_url":"https:\/\/www.phpmillion.com\/blog\/dead-man-switch-1-4-gets-visual-emails-composer\/","date":"2019-01-28"}]',
        'NEWS_DATE' =>'2021-08-02',
        'ENVATO_API_TOKEN'=>'',
        'DATABASE_VERSION'=>'2.4.1',
       ]);
    }
    public function oAuthClients(){

        DB::table('oauth_clients')->updateOrInsert([
            'id'=>1,
            'name' =>'Laravel Personal Access Client',
            'secret' => 'YtYAWKxjvKk22NpQmrti8v7QXto3pgvG6XpPTkRi',
            'redirect'=>'http://localhost',
            'personal_access_client'=>1,
            'password_client'=>0,
            'revoked'=>0
        ]);
    }

    public function personalClients(){
        DB::table('oauth_personal_access_clients')->updateOrInsert([
            'id'=>1,
            'client_id'=>1
        ]);
    }

}
