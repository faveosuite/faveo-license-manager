<?php

namespace Database\Factories;

use App\Models\AflSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflSettingsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflSettings::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ROOT_URL' => 'https://license.faveohelpdesk.com',
            'CLIENT_EMAIL' => 'admin@ladybirdweb.com',
            'LICENSE_CODE' => 'a607e11e-5949-4339-a182-e49f2bd51186',
            'INSTALLATION_HASH' => '0b656b2163477d0aa1fa698d014fba16dd03703273b2c205ca14576560a13d89',
            'SYSTEM_LANGUAGE' => 'en',
            'TIMEZONE' => 'UTC',
            'RECORDS_ARCHIVE_DAYS' => '365',
            'RECORDS_ON_ADMIN_PAGE' => '10',
            'RECORDS_ON_INDEX_PAGE' => '5',
            'RECORDS_ON_SEARCH_PAGE' => '10',
            'API_STATUS' => '1',
            'BANNED_HOSTS' => '0',
            'BANNED_HOST_MESSAGE' => '',
            'FAILED_LOGINS_LIMIT' => '0',
            'FAILED_LICENSINGS_LIMIT' => '0',
            'FAILED_HOSTS_FORGET' => '1',
            'SMART_REPORTS' => '1',
            'SMART_TABLES' => '1',
            'MIN_PASSWORD_LENGTH' => '8',
            'WHITELISTED_ACCESS' => '0',
            'WHITELISTED_IP' => '',
            'EMAIL_FROM_NAME' => 'Faveo Helpdesk',
            'EMAIL_FROM_ADDRESS' => 'support@faveohelpdesk.com',
            'EMAIL_CC_ADMIN' => '0',
            'EMAIL_EXPIRING_LICENSE_DAYS' => '0',
            'EMAIL_EXPIRING_UPDATES_DAYS' => '0',
            'EMAIL_EXPIRING_SUPPORT_DAYS' => '0',
            'EXPIRATION_CHECK_DATE' => '2021-08-04',
            'DATABASE_CLEANUP_ENABLED' => '1',
            'DATABASE_CLEANUP_CALLBACKS' => '90',
            'DATABASE_CLEANUP_REPORTS_MAIN' => '90',
            'DATABASE_CLEANUP_REPORTS_SYSTEM' => '60',
            'DATABASE_CLEANUP_REPORTS_LICENSES' => '0',
            'DATABASE_CLEANUP_DATE' => '2021-08-04',
            'NEWS_TEXT' => '[{"title":"phpmillion Scripts Receive New Features and Core Updates","full_url":"https:\/\/www.phpmillion.com\/blog\/phpmillion-scripts-receive-new-features-and-core-updates\/","date":"2019-08-19"},{"title":"Force User Input Validation with Auto PHP Licenser 2.5","full_url":"https:\/\/www.phpmillion.com\/blog\/force-user-input-validation-with-auto-php-licenser-2-5\/","date":"2019-04-18"},{"title":"Dead Man Switch 1.4 Gets Visual Emails Composer","full_url":"https:\/\/www.phpmillion.com\/blog\/dead-man-switch-1-4-gets-visual-emails-composer\/","date":"2019-01-28"}]',
            'NEWS_DATE' => '2021-08-02',
            'ENVATO_API_TOKEN' => '',
            'DATABASE_VERSION' => '2.4.1',

        ];
    }
}
