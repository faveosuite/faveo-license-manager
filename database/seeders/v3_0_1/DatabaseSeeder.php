<?php

namespace Database\Seeders\v3_0_1;

use App\Models\CommonSetting;
use App\Models\DateFormat;
use App\Models\TimeFormat;
use App\Models\Timezone;
use App\Models\AflSettings;
use App\Models\ScheduleCron;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder {

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->seedEmail();
        $this->cronsTable();
        $this->settingsTable();
    }

    private function seedEmail()
    {

        $this->insertEnvKeysIfNotPresent();
        $this->dateFormatSeeder();
        $this->timeFormatSeeder();
        $this->timezonesSeeder();
        $this->commonSettingSeeder();

        $originalConfigKeys = [
            'MAIL_DRIVER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_USERNAME',
        ];
        $configValues = AflSettings::first();
        $configValues->EMAIL_DRIVER = env('MAIL_DRIVER', '');
        $configValues->EMAIL_HOST = env('MAIL_HOST', '');
        $configValues->EMAIL_PORT = env('MAIL_PORT', null);
        $configValues->EMAIL_PASSWORD = env('MAIL_PASSWORD', '');
        $configValues->EMAIL_FROM_NAME = env('MAIL_USERNAME', '');
        $configValues->EMAIL_ENCRYPTION = env('MAIL_ENCRYPTION', '');
        $configValues->save();

        foreach ($originalConfigKeys as $key) {
            putenv($key);
        }

    }

    private function insertEnvKeysIfNotPresent(){
        $recaptchaSiteKey = '';
        $viteRecaptchaSiteKey = '"${RECAPTCHA_SITE_KEY}"';

        // Check if the variables are already defined in the .env file
        if (!$this->envVariableExists('RECAPTCHA_SITE_KEY')) {
            // Write to the .env file
            File::append('.env', PHP_EOL . "RECAPTCHA_SITE_KEY={$recaptchaSiteKey}");
        }

        if (!$this->envVariableExists('VITE_RECAPTCHA_SITE_KEY')) {
            // Write to the .env file
            File::append('.env', PHP_EOL . "VITE_RECAPTCHA_SITE_KEY={$viteRecaptchaSiteKey}");
        }
    }

    private function envVariableExists($key)
    {
        $envFilePath = base_path('.env');
        $envContent = file_get_contents($envFilePath);
        $pattern = "/^{$key}=/m";

        return preg_match($pattern, $envContent);
    }

    private function dateFormatSeeder()
    {
        $date_formats = [
            ['format' => 'dd/mm/yyyy', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'dd-mm-yyyy', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'dd.mm.yyyy', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'mm/dd/yyyy', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'mm:dd:yyyy', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'mm-dd-yyyy', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'yyyy/mm/dd', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'yyyy.mm.dd', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'yyyy-mm-dd', 'js_format' => '', 'is_active' => '0'],
            ['format' => 'd-m-Y', 'js_format' => 'DD-MM-YYYY', 'is_active' => '1'],
            ['format' => 'm-d-Y', 'js_format' => 'MM-DD-YYYY', 'is_active' => '1'],
            ['format' => 'Y-m-d', 'js_format' => 'YYYY-MM-DD', 'is_active' => '1'],
            ['format' => 'F j, Y', 'js_format' => 'LL', 'is_active' => '1'],
        ];

        foreach ($date_formats as $format) {
            DateFormat::updateOrCreate(
                ['format' => $format['format']],
                [
                    'js_format' => $format['js_format'],
                    'is_active' => $format['is_active']
                ]
            );
        }
    }

    private function timeFormatSeeder()
    {
        $timeformats = [
            ['format' => 'H:i:s', 'hours' => '24 Hours', 'js_format' => 'HH:mm', 'is_active' => 1],
            ['format' => 'H.i.s', 'hours' => '', 'js_format' => '','is_active' => 0],
            ['format' => 'g:i a', 'hours' => '12 Hours', 'js_format' => 'hh:mm a', 'is_active' => 1],
        ];

        foreach ($timeformats as $format) {
            TimeFormat::updateOrCreate(
                ['format' => $format['format']],
                [
                    'hours' => $format['hours'] ?? null,
                    'js_format' => $format['js_format'] ?? null,
                    'is_active' => $format['is_active']
                ]
            );
        }
    }

    private function timezonesSeeder()
    {

        // Timezones
        $timezones = [
            'Pacific/Midway' => '(GMT-11:00) Midway Island',
            'US/Samoa' => '(GMT-11:00) Samoa',
            'US/Hawaii' => '(GMT-10:00) Hawaii',
            'US/Alaska' => '(GMT-09:00) Alaska',
            'US/Pacific' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
            'America/Tijuana' => '(GMT-08:00) Tijuana',
            'US/Arizona' => '(GMT-07:00) Arizona',
            'US/Mountain' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua',
            'America/Mazatlan' => '(GMT-07:00) Mazatlan',
            'America/Mexico_City' => '(GMT-06:00) Mexico City',
            'America/Monterrey' => '(GMT-06:00) Monterrey',
            'America/Santo_Domingo' => '(GMT-04:00) Santo_Domingo',
            'Canada/Saskatchewan' => '(GMT-06:00) Saskatchewan',
            'US/Central' => '(GMT-06:00) Central Time (US &amp; Canada)',
            'US/Eastern' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
            'US/East-Indiana' => '(GMT-05:00) Indiana (East)',
            'America/Bogota' => '(GMT-05:00) Bogota',
            'America/Lima' => '(GMT-05:00) Lima',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'Canada/Atlantic' => '(GMT-04:00) Atlantic Time (Canada)',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'Canada/Newfoundland' => '(GMT-03:30) Newfoundland',
            'America/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
            'America/Godthab' => '(GMT-03:00) Greenland',
            'Atlantic/Stanley' => '(GMT-02:00) Stanley',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Africa/Casablanca' => '(GMT) Casablanca',
            'Europe/Dublin' => '(GMT) Dublin',
            'Europe/Lisbon' => '(GMT) Lisbon',
            'Europe/London' => '(GMT) London',
            'Africa/Monrovia' => '(GMT) Monrovia',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade',
            'Europe/Berlin' => '(GMT+01:00) Berlin',
            'Europe/Bratislava' => '(GMT+01:00) Bratislava',
            'Europe/Brussels' => '(GMT+01:00) Brussels',
            'Europe/Budapest' => '(GMT+01:00) Budapest',
            'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
            'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
            'Europe/Madrid' => '(GMT+01:00) Madrid',
            'Europe/Paris' => '(GMT+01:00) Paris',
            'Europe/Prague' => '(GMT+01:00) Prague',
            'Europe/Rome' => '(GMT+01:00) Rome',
            'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
            'Europe/Skopje' => '(GMT+01:00) Skopje',
            'Europe/Stockholm' => '(GMT+01:00) Stockholm',
            'Europe/Vienna' => '(GMT+01:00) Vienna',
            'Europe/Warsaw' => '(GMT+01:00) Warsaw',
            'Europe/Zagreb' => '(GMT+01:00) Zagreb',
            'Europe/Athens' => '(GMT+02:00) Athens',
            'Europe/Bucharest' => '(GMT+02:00) Bucharest',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Africa/Harare' => '(GMT+02:00) Harare',
            'Europe/Helsinki' => '(GMT+02:00) Helsinki',
            'Europe/Istanbul' => '(GMT+02:00) Istanbul',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Europe/Kiev' => '(GMT+02:00) Kyiv',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Europe/Riga' => '(GMT+02:00) Riga',
            'Europe/Sofia' => '(GMT+02:00) Sofia',
            'Europe/Tallinn' => '(GMT+02:00) Tallinn',
            'Europe/Vilnius' => '(GMT+02:00) Vilnius',
            'Asia/Baghdad' => '(GMT+03:00) Baghdad',
            'Asia/Kuwait' => '(GMT+03:00) Kuwait',
            'Africa/Nairobi' => '(GMT+03:00) Nairobi',
            'Asia/Riyadh' => '(GMT+03:00) Riyadh',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Europe/Moscow' => '(GMT+04:00) Moscow',
            'Asia/Baku' => '(GMT+04:00) Baku',
            'Europe/Volgograd' => '(GMT+04:00) Volgograd',
            'Asia/Muscat' => '(GMT+04:00) Muscat',
            'Asia/Dubai' => '(GMT+04:00) Dubai',
            'Asia/Tbilisi' => '(GMT+04:00) Tbilisi',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Kabul' => '(GMT+04:30) Kabul',
            'Asia/Karachi' => '(GMT+05:00) Karachi',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Kolkata',
            'Asia/Kathmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Yekaterinburg' => '(GMT+06:00) Ekaterinburg',
            'Asia/Almaty' => '(GMT+06:00) Almaty',
            'Asia/Dhaka' => '(GMT+06:00) Dhaka',
            'Asia/Novosibirsk' => '(GMT+07:00) Novosibirsk',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok',
            'Asia/Ho_Chi_Minh' => '(GMT+07:00) Ho Chi Minh',
            'Asia/Jakarta' => '(GMT+07:00) Jakarta',
            'Asia/Krasnoyarsk' => '(GMT+08:00) Krasnoyarsk',
            'Asia/Chongqing' => '(GMT+08:00) Chongqing',
            'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Asia/Singapore' => '(GMT+08:00) Singapore',
            'Asia/Taipei' => '(GMT+08:00) Taipei',
            'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
            'Asia/Urumqi' => '(GMT+08:00) Urumqi',
            'Asia/Irkutsk' => '(GMT+09:00) Irkutsk',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Tokyo' => '(GMT+09:00) Tokyo',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Asia/Yakutsk' => '(GMT+10:00) Yakutsk',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Canberra' => '(GMT+10:00) Canberra',
            'Pacific/Guam' => '(GMT+10:00) Guam',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Australia/Melbourne' => '(GMT+10:00) Melbourne',
            'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
            'Australia/Sydney' => '(GMT+10:00) Sydney',
            'Asia/Vladivostok' => '(GMT+11:00) Vladivostok',
            'Asia/Magadan' => '(GMT+12:00) Magadan',
            'Pacific/Auckland' => '(GMT+12:00) Auckland',
            'Pacific/Fiji' => '(GMT+12:00) Fiji',
            'Asia/Manila' => '(GMT+08:00) Manila'
        ];

        foreach ($timezones as $name => $location) {
            Timezone::updateOrCreate(
                ['name' => $name],
                ['location' => $location]
            );
        }
    }

    private function commonSettingSeeder()
    {
        $settings = [
            'google_site_key' => '',
            'google_secret_key' => '',
            'agora_invoicing_url' => '',
            'timezone' => '81',
            'date_format' => '13',
            'time_format' => '3',
        ];
        foreach ($settings as $key => $value) {
            CommonSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
