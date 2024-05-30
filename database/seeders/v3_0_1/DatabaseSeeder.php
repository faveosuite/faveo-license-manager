<?php

namespace Database\Seeders\v3_0_1;

use App\Models\CommonSetting;
use App\Models\DateFormat;
use App\Models\TimeFormat;
use App\Models\Timezone;
use App\Models\AflSettings;
use App\Models\CountryCode;
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
        $this->dateFormatSeeder();
        $this->timeFormatSeeder();
        $this->timezonesSeeder();
        $this->commonSettingSeeder();
        $this->countryCodeTable();
    }

    private function seedEmail()
    {

        if(file_exists(base_path('.env'))) {
            $this->insertEnvKeysIfNotPresent();
        }

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

    public function cronsTable()
    {

        $crons = [
            ['scenario' => 'callback-cleanup', 'value' => 'everyMinute', 'command' => 'app:crack-callback-cleanup', 'status' => 1, 'icon' => 'glyphicon glyphicon-random', 'job_info' => 'callback_cleanup_tooltip', 'created_at' => now(), 'updated_at' => now()],
            ['scenario' => 'crack-reports-cleanup', 'value' => 'everyMinute', 'command' => 'app:crack-reports-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-modal-window', 'job_info' => 'crack_cleanup_tooltip', 'created_at' => now(), 'updated_at' => now()],
            ['scenario' => 'license-reports-cleanup', 'value' => 'everyMinute', 'command' => 'app:license-reports-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-tags', 'job_info' => 'license_report_cleanup_tooltip', 'created_at' => now(), 'updated_at' => now()],
            ['scenario' => 'system-reports-cleanup', 'value' => 'everyMinute', 'command' => 'app:system-reports-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-wrench', 'job_info' => 'system_cleanup_tooltip', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($crons as $cron) {
            ScheduleCron::updateOrcreate(['scenario' => $cron['scenario']], $cron);
        }
    }

    public function settingsTable()
    {
        AflSettings::first()->update([
            'WHITELISTED_ACCESS' => 0,
            'FAILED_FORGET_LIMIT' => 3,
            'FAILED_LOGINS_LIMIT' => 3,
            'BANNED_HOSTS' => 0,
        ]);
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
                ]);
        }
    }
    public function countryCodeTable()
    {
        $countryCodes =
            [
                [
                    "name" => "Ascension Island",
                    "nickname" => "ASCENSION ISLAND",
                    "iso2" => "AC",
                    "iso3" => "ASC",
                    "number_code" => -1,
                    "phone_code" => "247",
                    "example" => "40123"
                ],
                [
                    "name" => "Andorra",
                    "nickname" => "ANDORRA",
                    "iso2" => "AD",
                    "iso3" => "AND",
                    "number_code" => "020",
                    "phone_code" => "376",
                    "example" => "312 345"
                ],
                [
                    "name" => "United Arab Emirates",
                    "nickname" => "UNITED ARAB EMIRATES",
                    "iso2" => "AE",
                    "iso3" => "ARE",
                    "number_code" => "784",
                    "phone_code" => "971",
                    "example" => "050 123 4567"
                ],
                [
                    "name" => "Afghanistan",
                    "nickname" => "AFGHANISTAN",
                    "iso2" => "AF",
                    "iso3" => "AFG",
                    "number_code" => "004",
                    "phone_code" => "93",
                    "example" => "070 123 4567"
                ],
                [
                    "name" => "Antigua And Barbuda",
                    "nickname" => "ANTIGUA AND BARBUDA",
                    "iso2" => "AG",
                    "iso3" => "ATG",
                    "number_code" => "028",
                    "phone_code" => "1",
                    "example" => "(268) 464-1234"
                ],
                [
                    "name" => "Anguilla",
                    "nickname" => "ANGUILLA",
                    "iso2" => "AI",
                    "iso3" => "AIA",
                    "number_code" => "660",
                    "phone_code" => "1",
                    "example" => "(264) 235-1234"
                ],
                [
                    "name" => "Albania",
                    "nickname" => "ALBANIA",
                    "iso2" => "AL",
                    "iso3" => "ALB",
                    "number_code" => "008",
                    "phone_code" => "355",
                    "example" => "067 212 3456"
                ],
                [
                    "name" => "Armenia",
                    "nickname" => "ARMENIA",
                    "iso2" => "AM",
                    "iso3" => "ARM",
                    "number_code" => "051",
                    "phone_code" => "374",
                    "example" => "077 123456"
                ],
                [
                    "name" => "Angola",
                    "nickname" => "ANGOLA",
                    "iso2" => "AO",
                    "iso3" => "AGO",
                    "number_code" => "024",
                    "phone_code" => "244",
                    "example" => "923 123 456"
                ],
                [
                    "name" => "Argentina",
                    "nickname" => "ARGENTINA",
                    "iso2" => "AR",
                    "iso3" => "ARG",
                    "number_code" => "032",
                    "phone_code" => "54",
                    "example" => "011 15-2345-6789"
                ],
                [
                    "name" => "American Samoa",
                    "nickname" => "AMERICAN SAMOA",
                    "iso2" => "AS",
                    "iso3" => "ASM",
                    "number_code" => "016",
                    "phone_code" => "1",
                    "example" => "(684) 733-1234"
                ],
                [
                    "name" => "Austria",
                    "nickname" => "AUSTRIA",
                    "iso2" => "AT",
                    "iso3" => "AUT",
                    "number_code" => "040",
                    "phone_code" => "43",
                    "example" => "0664 123456"
                ],
                [
                    "name" => "Australia",
                    "nickname" => "AUSTRALIA",
                    "iso2" => "AU",
                    "iso3" => "AUS",
                    "number_code" => "036",
                    "phone_code" => "61",
                    "example" => "0412 345 678"
                ],
                [
                    "name" => "Aruba",
                    "nickname" => "ARUBA",
                    "iso2" => "AW",
                    "iso3" => "ABW",
                    "number_code" => "533",
                    "phone_code" => "297",
                    "example" => "560 1234"
                ],
                [
                    "name" => "Aland Islands",
                    "nickname" => "ALAND ISLANDS",
                    "iso2" => "AX",
                    "iso3" => "ALA",
                    "number_code" => "248",
                    "phone_code" => "358",
                    "example" => "041 2345678"
                ],
                [
                    "name" => "Azerbaijan",
                    "nickname" => "AZERBAIJAN",
                    "iso2" => "AZ",
                    "iso3" => "AZE",
                    "number_code" => "031",
                    "phone_code" => "994",
                    "example" => "040 123 45 67"
                ],
                [
                    "name" => "Bosnia And Herzegovina",
                    "nickname" => "BOSNIA AND HERZEGOVINA",
                    "iso2" => "BA",
                    "iso3" => "BIH",
                    "number_code" => "070",
                    "phone_code" => "387",
                    "example" => "061 123 456"
                ],
                [
                    "name" => "Barbados",
                    "nickname" => "BARBADOS",
                    "iso2" => "BB",
                    "iso3" => "BRB",
                    "number_code" => "052",
                    "phone_code" => "1",
                    "example" => "(246) 250-1234"
                ],
                [
                    "name" => "Bangladesh",
                    "nickname" => "BANGLADESH",
                    "iso2" => "BD",
                    "iso3" => "BGD",
                    "number_code" => "050",
                    "phone_code" => "880",
                    "example" => "01812-345678"
                ],
                [
                    "name" => "Belgium",
                    "nickname" => "BELGIUM",
                    "iso2" => "BE",
                    "iso3" => "BEL",
                    "number_code" => "056",
                    "phone_code" => "32",
                    "example" => "0470 12 34 56"
                ],
                [
                    "name" => "Burkina Faso",
                    "nickname" => "BURKINA FASO",
                    "iso2" => "BF",
                    "iso3" => "BFA",
                    "number_code" => "854",
                    "phone_code" => "226",
                    "example" => "70 12 34 56"
                ],
                [
                    "name" => "Bulgaria",
                    "nickname" => "BULGARIA",
                    "iso2" => "BG",
                    "iso3" => "BGR",
                    "number_code" => "100",
                    "phone_code" => "359",
                    "example" => "043 012 345"
                ],
                [
                    "name" => "Bahrain",
                    "nickname" => "BAHRAIN",
                    "iso2" => "BH",
                    "iso3" => "BHR",
                    "number_code" => "048",
                    "phone_code" => "973",
                    "example" => "3600 1234"
                ],
                [
                    "name" => "Burundi",
                    "nickname" => "BURUNDI",
                    "iso2" => "BI",
                    "iso3" => "BDI",
                    "number_code" => "108",
                    "phone_code" => "257",
                    "example" => "79 56 12 34"
                ],
                [
                    "name" => "Benin",
                    "nickname" => "BENIN",
                    "iso2" => "BJ",
                    "iso3" => "BEN",
                    "number_code" => "204",
                    "phone_code" => "229",
                    "example" => "90 01 12 34"
                ],
                [
                    "name" => "Saint Barthelemy",
                    "nickname" => "SAINT BARTHELEMY",
                    "iso2" => "BL",
                    "iso3" => "BLM",
                    "number_code" => "652",
                    "phone_code" => "590",
                    "example" => "0690 00 12 34"
                ],
                [
                    "name" => "Bermuda",
                    "nickname" => "BERMUDA",
                    "iso2" => "BM",
                    "iso3" => "BMU",
                    "number_code" => "060",
                    "phone_code" => "1",
                    "example" => "(441) 370-1234"
                ],
                [
                    "name" => "Brunei Darussalam",
                    "nickname" => "BRUNEI DARUSSALAM",
                    "iso2" => "BN",
                    "iso3" => "BRN",
                    "number_code" => "096",
                    "phone_code" => "673",
                    "example" => "712 3456"
                ],
                [
                    "name" => "Bolivia",
                    "nickname" => "BOLIVIA",
                    "iso2" => "BO",
                    "iso3" => "BOL",
                    "number_code" => "068",
                    "phone_code" => "591",
                    "example" => "71234567"
                ],
                [
                    "name" => "Caribbean Netherlands",
                    "nickname" => "CARIBBEAN NETHERLANDS",
                    "iso2" => "BQ",
                    "iso3" => "BES",
                    "number_code" => "535",
                    "phone_code" => "599",
                    "example" => "318 1234"
                ],
                [
                    "name" => "Brazil",
                    "nickname" => "BRAZIL",
                    "iso2" => "BR",
                    "iso3" => "BRA",
                    "number_code" => "076",
                    "phone_code" => "55",
                    "example" => "(11) 96123-4567"
                ],
                [
                    "name" => "Bahamas",
                    "nickname" => "BAHAMAS",
                    "iso2" => "BS",
                    "iso3" => "BHS",
                    "number_code" => "044",
                    "phone_code" => "1",
                    "example" => "(242) 359-1234"
                ],
                [
                    "name" => "Bhutan",
                    "nickname" => "BHUTAN",
                    "iso2" => "BT",
                    "iso3" => "BTN",
                    "number_code" => "064",
                    "phone_code" => "975",
                    "example" => "17 12 34 56"
                ],
                [
                    "name" => "Botswana",
                    "nickname" => "BOTSWANA",
                    "iso2" => "BW",
                    "iso3" => "BWA",
                    "number_code" => "072",
                    "phone_code" => "267",
                    "example" => "71 123 456"
                ],
                [
                    "name" => "Belarus",
                    "nickname" => "BELARUS",
                    "iso2" => "BY",
                    "iso3" => "BLR",
                    "number_code" => "112",
                    "phone_code" => "375",
                    "example" => "8 029 491-19-11"
                ],
                [
                    "name" => "Belize",
                    "nickname" => "BELIZE",
                    "iso2" => "BZ",
                    "iso3" => "BLZ",
                    "number_code" => "084",
                    "phone_code" => "501",
                    "example" => "622-1234"
                ],
                [
                    "name" => "Canada",
                    "nickname" => "CANADA",
                    "iso2" => "CA",
                    "iso3" => "CAN",
                    "number_code" => "124",
                    "phone_code" => "1",
                    "example" => "(506) 234-5678"
                ],
                [
                    "name" => "Cocos (Keeling) Islands",
                    "nickname" => "COCOS (KEELING) ISLANDS",
                    "iso2" => "CC",
                    "iso3" => "CCK",
                    "number_code" => "166",
                    "phone_code" => "61",
                    "example" => "0412 345 678"
                ],
                [
                    "name" => "Congo, Democratic Republic",
                    "nickname" => "CONGO, DEMOCRATIC REPUBLIC",
                    "iso2" => "CD",
                    "iso3" => "COD",
                    "number_code" => "180",
                    "phone_code" => "243",
                    "example" => "0991 234 567"
                ],
                [
                    "name" => "Central African Republic",
                    "nickname" => "CENTRAL AFRICAN REPUBLIC",
                    "iso2" => "CF",
                    "iso3" => "CAF",
                    "number_code" => "140",
                    "phone_code" => "236",
                    "example" => "70 01 23 45"
                ],
                [
                    "name" => "Congo",
                    "nickname" => "CONGO",
                    "iso2" => "CG",
                    "iso3" => "COG",
                    "number_code" => "178",
                    "phone_code" => "242",
                    "example" => "06 123 4567"
                ],
                [
                    "name" => "Switzerland",
                    "nickname" => "SWITZERLAND",
                    "iso2" => "CH",
                    "iso3" => "CHE",
                    "number_code" => "756",
                    "phone_code" => "41",
                    "example" => "078 123 45 67"
                ],
                [
                    "name" => "Cote D\"Ivoire",
                    "nickname" => "COTE D\"IVOIRE",
                    "iso2" => "CI",
                    "iso3" => "CIV",
                    "number_code" => "384",
                    "phone_code" => "225",
                    "example" => "01 23 45 6789"
                ],
                [
                    "name" => "Cook Islands",
                    "nickname" => "COOK ISLANDS",
                    "iso2" => "CK",
                    "iso3" => "COK",
                    "number_code" => "184",
                    "phone_code" => "682",
                    "example" => "71 234"
                ],
                [
                    "name" => "Chile",
                    "nickname" => "CHILE",
                    "iso2" => "CL",
                    "iso3" => "CHL",
                    "number_code" => "152",
                    "phone_code" => "56",
                    "example" => "(2) 2123 4567"
                ],
                [
                    "name" => "Cameroon",
                    "nickname" => "CAMEROON",
                    "iso2" => "CM",
                    "iso3" => "CMR",
                    "number_code" => "120",
                    "phone_code" => "237",
                    "example" => "6 71 23 45 67"
                ],
                [
                    "name" => "China",
                    "nickname" => "CHINA",
                    "iso2" => "CN",
                    "iso3" => "CHN",
                    "number_code" => "156",
                    "phone_code" => "86",
                    "example" => "131 2345 6789"
                ],
                [
                    "name" => "Colombia",
                    "nickname" => "COLOMBIA",
                    "iso2" => "CO",
                    "iso3" => "COL",
                    "number_code" => "170",
                    "phone_code" => "57",
                    "example" => "321 1234567"
                ],
                [
                    "name" => "Costa Rica",
                    "nickname" => "COSTA RICA",
                    "iso2" => "CR",
                    "iso3" => "CRI",
                    "number_code" => "188",
                    "phone_code" => "506",
                    "example" => "8312 3456"
                ],
                [
                    "name" => "Cuba",
                    "nickname" => "CUBA",
                    "iso2" => "CU",
                    "iso3" => "CUB",
                    "number_code" => "192",
                    "phone_code" => "53",
                    "example" => "05 1234567"
                ],
                [
                    "name" => "Cape Verde",
                    "nickname" => "CAPE VERDE",
                    "iso2" => "CV",
                    "iso3" => "CPV",
                    "number_code" => "132",
                    "phone_code" => "238",
                    "example" => "991 12 34"
                ],
                [
                    "name" => "Curaçao",
                    "nickname" => "CURAÇAO",
                    "iso2" => "CW",
                    "iso3" => "CUW",
                    "number_code" => "531",
                    "phone_code" => "599",
                    "example" => "9 518 1234"
                ],
                [
                    "name" => "Christmas Island",
                    "nickname" => "CHRISTMAS ISLAND",
                    "iso2" => "CX",
                    "iso3" => "CXR",
                    "number_code" => "162",
                    "phone_code" => "61",
                    "example" => "0412 345 678"
                ],
                [
                    "name" => "Cyprus",
                    "nickname" => "CYPRUS",
                    "iso2" => "CY",
                    "iso3" => "CYP",
                    "number_code" => "196",
                    "phone_code" => "357",
                    "example" => "96 123456"
                ],
                [
                    "name" => "Czech Republic",
                    "nickname" => "CZECH REPUBLIC",
                    "iso2" => "CZ",
                    "iso3" => "CZE",
                    "number_code" => "203",
                    "phone_code" => "420",
                    "example" => "601 123 456"
                ],
                [
                    "name" => "Germany",
                    "nickname" => "GERMANY",
                    "iso2" => "DE",
                    "iso3" => "DEU",
                    "number_code" => "276",
                    "phone_code" => "49",
                    "example" => "01512 3456789"
                ],
                [
                    "name" => "Djibouti",
                    "nickname" => "DJIBOUTI",
                    "iso2" => "DJ",
                    "iso3" => "DJI",
                    "number_code" => "262",
                    "phone_code" => "253",
                    "example" => "77 83 10 01"
                ],
                [
                    "name" => "Denmark",
                    "nickname" => "DENMARK",
                    "iso2" => "DK",
                    "iso3" => "DNK",
                    "number_code" => "208",
                    "phone_code" => "45",
                    "example" => "34 41 23 45"
                ],
                [
                    "name" => "Dominica",
                    "nickname" => "DOMINICA",
                    "iso2" => "DM",
                    "iso3" => "DMA",
                    "number_code" => "212",
                    "phone_code" => "1",
                    "example" => "(767) 225-1234"
                ],
                [
                    "name" => "Dominican Republic",
                    "nickname" => "DOMINICAN REPUBLIC",
                    "iso2" => "DO",
                    "iso3" => "DOM",
                    "number_code" => "214",
                    "phone_code" => "1",
                    "example" => "(809) 234-5678"
                ],
                [
                    "name" => "Algeria",
                    "nickname" => "ALGERIA",
                    "iso2" => "DZ",
                    "iso3" => "DZA",
                    "number_code" => "012",
                    "phone_code" => "213",
                    "example" => "0551 23 45 67"
                ],
                [
                    "name" => "Ecuador",
                    "nickname" => "ECUADOR",
                    "iso2" => "EC",
                    "iso3" => "ECU",
                    "number_code" => "218",
                    "phone_code" => "593",
                    "example" => "099 123 4567"
                ],
                [
                    "name" => "Estonia",
                    "nickname" => "ESTONIA",
                    "iso2" => "EE",
                    "iso3" => "EST",
                    "number_code" => "233",
                    "phone_code" => "372",
                    "example" => "5123 4567"
                ],
                [
                    "name" => "Egypt",
                    "nickname" => "EGYPT",
                    "iso2" => "EG",
                    "iso3" => "EGY",
                    "number_code" => "818",
                    "phone_code" => "20",
                    "example" => "010 01234567"
                ],
                [
                    "name" => "Western Sahara",
                    "nickname" => "WESTERN SAHARA",
                    "iso2" => "EH",
                    "iso3" => "ESH",
                    "number_code" => "732",
                    "phone_code" => "212",
                    "example" => "0650-123456"
                ],
                [
                    "name" => "Eritrea",
                    "nickname" => "ERITREA",
                    "iso2" => "ER",
                    "iso3" => "ERI",
                    "number_code" => "232",
                    "phone_code" => "291",
                    "example" => "07 123 456"
                ],
                [
                    "name" => "Spain",
                    "nickname" => "SPAIN",
                    "iso2" => "ES",
                    "iso3" => "ESP",
                    "number_code" => "724",
                    "phone_code" => "34",
                    "example" => "612 34 56 78"
                ],
                [
                    "name" => "Ethiopia",
                    "nickname" => "ETHIOPIA",
                    "iso2" => "ET",
                    "iso3" => "ETH",
                    "number_code" => "231",
                    "phone_code" => "251",
                    "example" => "091 123 4567"
                ],
                [
                    "name" => "Finland",
                    "nickname" => "FINLAND",
                    "iso2" => "FI",
                    "iso3" => "FIN",
                    "number_code" => "246",
                    "phone_code" => "358",
                    "example" => "041 2345678"
                ],
                [
                    "name" => "Fiji",
                    "nickname" => "FIJI",
                    "iso2" => "FJ",
                    "iso3" => "FJI",
                    "number_code" => "242",
                    "phone_code" => "679",
                    "example" => "701 2345"
                ],
                [
                    "name" => "Falkland Islands (Malvinas)",
                    "nickname" => "FALKLAND ISLANDS (MALVINAS)",
                    "iso2" => "FK",
                    "iso3" => "FLK",
                    "number_code" => "238",
                    "phone_code" => "500",
                    "example" => "51234"
                ],
                [
                    "name" => "Micronesia, Federated States Of",
                    "nickname" => "MICRONESIA, FEDERATED STATES OF",
                    "iso2" => "FM",
                    "iso3" => "FSM",
                    "number_code" => "583",
                    "phone_code" => "691",
                    "example" => "350 1234"
                ],
                [
                    "name" => "Faroe Islands",
                    "nickname" => "FAROE ISLANDS",
                    "iso2" => "FO",
                    "iso3" => "FRO",
                    "number_code" => "234",
                    "phone_code" => "298",
                    "example" => "211234"
                ],
                [
                    "name" => "France",
                    "nickname" => "FRANCE",
                    "iso2" => "FR",
                    "iso3" => "FRA",
                    "number_code" => "250",
                    "phone_code" => "33",
                    "example" => "06 12 34 56 78"
                ],
                [
                    "name" => "Gabon",
                    "nickname" => "GABON",
                    "iso2" => "GA",
                    "iso3" => "GAB",
                    "number_code" => "266",
                    "phone_code" => "241",
                    "example" => "06 03 12 34"
                ],
                [
                    "name" => "United Kingdom",
                    "nickname" => "UNITED KINGDOM",
                    "iso2" => "GB",
                    "iso3" => "GBR",
                    "number_code" => "826",
                    "phone_code" => "44",
                    "example" => "07400 123456"
                ],
                [
                    "name" => "Grenada",
                    "nickname" => "GRENADA",
                    "iso2" => "GD",
                    "iso3" => "GRD",
                    "number_code" => "308",
                    "phone_code" => "1",
                    "example" => "(473) 403-1234"
                ],
                [
                    "name" => "Georgia",
                    "nickname" => "GEORGIA",
                    "iso2" => "GE",
                    "iso3" => "GEO",
                    "number_code" => "268",
                    "phone_code" => "995",
                    "example" => "555 12 34 56"
                ],
                [
                    "name" => "French Guiana",
                    "nickname" => "FRENCH GUIANA",
                    "iso2" => "GF",
                    "iso3" => "GUF",
                    "number_code" => "254",
                    "phone_code" => "594",
                    "example" => "0694 20 12 34"
                ],
                [
                    "name" => "Guernsey",
                    "nickname" => "GUERNSEY",
                    "iso2" => "GG",
                    "iso3" => "GGY",
                    "number_code" => "831",
                    "phone_code" => "44",
                    "example" => "07781 123456"
                ],
                [
                    "name" => "Ghana",
                    "nickname" => "GHANA",
                    "iso2" => "GH",
                    "iso3" => "GHA",
                    "number_code" => "288",
                    "phone_code" => "233",
                    "example" => "023 123 4567"
                ],
                [
                    "name" => "Gibraltar",
                    "nickname" => "GIBRALTAR",
                    "iso2" => "GI",
                    "iso3" => "GIB",
                    "number_code" => "292",
                    "phone_code" => "350",
                    "example" => "57123456"
                ],
                [
                    "name" => "Greenland",
                    "nickname" => "GREENLAND",
                    "iso2" => "GL",
                    "iso3" => "GRL",
                    "number_code" => "304",
                    "phone_code" => "299",
                    "example" => "22 12 34"
                ],
                [
                    "name" => "Gambia",
                    "nickname" => "GAMBIA",
                    "iso2" => "GM",
                    "iso3" => "GMB",
                    "number_code" => "270",
                    "phone_code" => "220",
                    "example" => "301 2345"
                ],
                [
                    "name" => "Guinea",
                    "nickname" => "GUINEA",
                    "iso2" => "GN",
                    "iso3" => "GIN",
                    "number_code" => "324",
                    "phone_code" => "224",
                    "example" => "601 12 34 56"
                ],
                [
                    "name" => "Guadeloupe",
                    "nickname" => "GUADELOUPE",
                    "iso2" => "GP",
                    "iso3" => "GLP",
                    "number_code" => "312",
                    "phone_code" => "590",
                    "example" => "0690 00 12 34"
                ],
                [
                    "name" => "Equatorial Guinea",
                    "nickname" => "EQUATORIAL GUINEA",
                    "iso2" => "GQ",
                    "iso3" => "GNQ",
                    "number_code" => "226",
                    "phone_code" => "240",
                    "example" => "222 123 456"
                ],
                [
                    "name" => "Greece",
                    "nickname" => "GREECE",
                    "iso2" => "GR",
                    "iso3" => "GRC",
                    "number_code" => "300",
                    "phone_code" => "30",
                    "example" => "691 234 5678"
                ],
                [
                    "name" => "Guatemala",
                    "nickname" => "GUATEMALA",
                    "iso2" => "GT",
                    "iso3" => "GTM",
                    "number_code" => "320",
                    "phone_code" => "502",
                    "example" => "5123 4567"
                ],
                [
                    "name" => "Guam",
                    "nickname" => "GUAM",
                    "iso2" => "GU",
                    "iso3" => "GUM",
                    "number_code" => "316",
                    "phone_code" => "1",
                    "example" => "(671) 300-1234"
                ],
                [
                    "name" => "Guinea-Bissau",
                    "nickname" => "GUINEA-BISSAU",
                    "iso2" => "GW",
                    "iso3" => "GNB",
                    "number_code" => "624",
                    "phone_code" => "245",
                    "example" => "955 012 345"
                ],
                [
                    "name" => "Guyana",
                    "nickname" => "GUYANA",
                    "iso2" => "GY",
                    "iso3" => "GUY",
                    "number_code" => "328",
                    "phone_code" => "592",
                    "example" => "609 1234"
                ],
                [
                    "name" => "Hong Kong",
                    "nickname" => "HONG KONG",
                    "iso2" => "HK",
                    "iso3" => "HKG",
                    "number_code" => "344",
                    "phone_code" => "852",
                    "example" => "5123 4567"
                ],
                [
                    "name" => "Honduras",
                    "nickname" => "HONDURAS",
                    "iso2" => "HN",
                    "iso3" => "HND",
                    "number_code" => "340",
                    "phone_code" => "504",
                    "example" => "9123-4567"
                ],
                [
                    "name" => "Croatia",
                    "nickname" => "CROATIA",
                    "iso2" => "HR",
                    "iso3" => "HRV",
                    "number_code" => "191",
                    "phone_code" => "385",
                    "example" => "092 123 4567"
                ],
                [
                    "name" => "Haiti",
                    "nickname" => "HAITI",
                    "iso2" => "HT",
                    "iso3" => "HTI",
                    "number_code" => "332",
                    "phone_code" => "509",
                    "example" => "34 10 1234"
                ],
                [
                    "name" => "Hungary",
                    "nickname" => "HUNGARY",
                    "iso2" => "HU",
                    "iso3" => "HUN",
                    "number_code" => "348",
                    "phone_code" => "36",
                    "example" => "06 20 123 4567"
                ],
                [
                    "name" => "Indonesia",
                    "nickname" => "INDONESIA",
                    "iso2" => "ID",
                    "iso3" => "IDN",
                    "number_code" => "360",
                    "phone_code" => "62",
                    "example" => "0812-345-678"
                ],
                [
                    "name" => "Ireland",
                    "nickname" => "IRELAND",
                    "iso2" => "IE",
                    "iso3" => "IRL",
                    "number_code" => "372",
                    "phone_code" => "353",
                    "example" => "085 012 3456"
                ],
                [
                    "name" => "Israel",
                    "nickname" => "ISRAEL",
                    "iso2" => "IL",
                    "iso3" => "ISR",
                    "number_code" => "376",
                    "phone_code" => "972",
                    "example" => "050-234-5678"
                ],
                [
                    "name" => "Isle Of Man",
                    "nickname" => "ISLE OF MAN",
                    "iso2" => "IM",
                    "iso3" => "IMN",
                    "number_code" => "833",
                    "phone_code" => "44",
                    "example" => "07924 123456"
                ],
                [
                    "name" => "India",
                    "nickname" => "INDIA",
                    "iso2" => "IN",
                    "iso3" => "IND",
                    "number_code" => "356",
                    "phone_code" => "91",
                    "example" => "081234 56789"
                ],
                [
                    "name" => "British Indian Ocean Territory",
                    "nickname" => "BRITISH INDIAN OCEAN TERRITORY",
                    "iso2" => "IO",
                    "iso3" => "IOT",
                    "number_code" => "086",
                    "phone_code" => "246",
                    "example" => "380 1234"
                ],
                [
                    "name" => "Iraq",
                    "nickname" => "IRAQ",
                    "iso2" => "IQ",
                    "iso3" => "IRQ",
                    "number_code" => "368",
                    "phone_code" => "964",
                    "example" => "0791 234 5678"
                ],
                [
                    "name" => "Iran, Islamic Republic Of",
                    "nickname" => "IRAN, ISLAMIC REPUBLIC OF",
                    "iso2" => "IR",
                    "iso3" => "IRN",
                    "number_code" => "364",
                    "phone_code" => "98",
                    "example" => "0912 345 6789"
                ],
                [
                    "name" => "Iceland",
                    "nickname" => "ICELAND",
                    "iso2" => "IS",
                    "iso3" => "ISL",
                    "number_code" => "352",
                    "phone_code" => "354",
                    "example" => "611 1234"
                ],
                [
                    "name" => "Italy",
                    "nickname" => "ITALY",
                    "iso2" => "IT",
                    "iso3" => "ITA",
                    "number_code" => "380",
                    "phone_code" => "39",
                    "example" => "312 345 6789"
                ],
                [
                    "name" => "Jersey",
                    "nickname" => "JERSEY",
                    "iso2" => "JE",
                    "iso3" => "JEY",
                    "number_code" => "832",
                    "phone_code" => "44",
                    "example" => "07797 712345"
                ],
                [
                    "name" => "Jamaica",
                    "nickname" => "JAMAICA",
                    "iso2" => "JM",
                    "iso3" => "JAM",
                    "number_code" => "388",
                    "phone_code" => "1",
                    "example" => "(876) 210-1234"
                ],
                [
                    "name" => "Jordan",
                    "nickname" => "JORDAN",
                    "iso2" => "JO",
                    "iso3" => "JOR",
                    "number_code" => "400",
                    "phone_code" => "962",
                    "example" => "07 9012 3456"
                ],
                [
                    "name" => "Japan",
                    "nickname" => "JAPAN",
                    "iso2" => "JP",
                    "iso3" => "JPN",
                    "number_code" => "392",
                    "phone_code" => "81",
                    "example" => "090-1234-5678"
                ],
                [
                    "name" => "Kenya",
                    "nickname" => "KENYA",
                    "iso2" => "KE",
                    "iso3" => "KEN",
                    "number_code" => "404",
                    "phone_code" => "254",
                    "example" => "0712 123456"
                ],
                [
                    "name" => "Kyrgyzstan",
                    "nickname" => "KYRGYZSTAN",
                    "iso2" => "KG",
                    "iso3" => "KGZ",
                    "number_code" => "417",
                    "phone_code" => "996",
                    "example" => "0700 123 456"
                ],
                [
                    "name" => "Cambodia",
                    "nickname" => "CAMBODIA",
                    "iso2" => "KH",
                    "iso3" => "KHM",
                    "number_code" => "116",
                    "phone_code" => "855",
                    "example" => "091 234 567"
                ],
                [
                    "name" => "Kiribati",
                    "nickname" => "KIRIBATI",
                    "iso2" => "KI",
                    "iso3" => "KIR",
                    "number_code" => "296",
                    "phone_code" => "686",
                    "example" => "72001234"
                ],
                [
                    "name" => "Comoros",
                    "nickname" => "COMOROS",
                    "iso2" => "KM",
                    "iso3" => "COM",
                    "number_code" => "174",
                    "phone_code" => "269",
                    "example" => "321 23 45"
                ],
                [
                    "name" => "Saint Kitts And Nevis",
                    "nickname" => "SAINT KITTS AND NEVIS",
                    "iso2" => "KN",
                    "iso3" => "KNA",
                    "number_code" => "659",
                    "phone_code" => "1",
                    "example" => "(869) 765-2917"
                ],
                [
                    "name" => "North Korea",
                    "nickname" => "NORTH KOREA",
                    "iso2" => "KP",
                    "iso3" => "PRK",
                    "number_code" => "408",
                    "phone_code" => "850",
                    "example" => "0192 123 4567"
                ],
                [
                    "name" => "Korea",
                    "nickname" => "KOREA",
                    "iso2" => "KR",
                    "iso3" => "KOR",
                    "number_code" => "410",
                    "phone_code" => "82",
                    "example" => "010-2000-0000"
                ],
                [
                    "name" => "Kuwait",
                    "nickname" => "KUWAIT",
                    "iso2" => "KW",
                    "iso3" => "KWT",
                    "number_code" => "414",
                    "phone_code" => "965",
                    "example" => "500 12345"
                ],
                [
                    "name" => "Cayman Islands",
                    "nickname" => "CAYMAN ISLANDS",
                    "iso2" => "KY",
                    "iso3" => "CYM",
                    "number_code" => "136",
                    "phone_code" => "1",
                    "example" => "(345) 323-1234"
                ],
                [
                    "name" => "Kazakhstan",
                    "nickname" => "KAZAKHSTAN",
                    "iso2" => "KZ",
                    "iso3" => "KAZ",
                    "number_code" => "398",
                    "phone_code" => "7",
                    "example" => "8 (771) 000 9998"
                ],
                [
                    "name" => "Lao People\"s Democratic Republic",
                    "nickname" => "LAO PEOPLE\"S DEMOCRATIC REPUBLIC",
                    "iso2" => "LA",
                    "iso3" => "LAO",
                    "number_code" => "418",
                    "phone_code" => "856",
                    "example" => "020 23 123 456"
                ],
                [
                    "name" => "Lebanon",
                    "nickname" => "LEBANON",
                    "iso2" => "LB",
                    "iso3" => "LBN",
                    "number_code" => "422",
                    "phone_code" => "961",
                    "example" => "71 123 456"
                ],
                [
                    "name" => "Saint Lucia",
                    "nickname" => "SAINT LUCIA",
                    "iso2" => "LC",
                    "iso3" => "LCA",
                    "number_code" => "662",
                    "phone_code" => "1",
                    "example" => "(758) 284-5678"
                ],
                [
                    "name" => "Liechtenstein",
                    "nickname" => "LIECHTENSTEIN",
                    "iso2" => "LI",
                    "iso3" => "LIE",
                    "number_code" => "438",
                    "phone_code" => "423",
                    "example" => "660 234 567"
                ],
                [
                    "name" => "Sri Lanka",
                    "nickname" => "SRI LANKA",
                    "iso2" => "LK",
                    "iso3" => "LKA",
                    "number_code" => "144",
                    "phone_code" => "94",
                    "example" => "071 234 5678"
                ],
                [
                    "name" => "Liberia",
                    "nickname" => "LIBERIA",
                    "iso2" => "LR",
                    "iso3" => "LBR",
                    "number_code" => "430",
                    "phone_code" => "231",
                    "example" => "077 012 3456"
                ],
                [
                    "name" => "Lesotho",
                    "nickname" => "LESOTHO",
                    "iso2" => "LS",
                    "iso3" => "LSO",
                    "number_code" => "426",
                    "phone_code" => "266",
                    "example" => "5012 3456"
                ],
                [
                    "name" => "Lithuania",
                    "nickname" => "LITHUANIA",
                    "iso2" => "LT",
                    "iso3" => "LTU",
                    "number_code" => "440",
                    "phone_code" => "370",
                    "example" => "(0-612) 34567"
                ],
                [
                    "name" => "Luxembourg",
                    "nickname" => "LUXEMBOURG",
                    "iso2" => "LU",
                    "iso3" => "LUX",
                    "number_code" => "442",
                    "phone_code" => "352",
                    "example" => "628 123 456"
                ],
                [
                    "name" => "Latvia",
                    "nickname" => "LATVIA",
                    "iso2" => "LV",
                    "iso3" => "LVA",
                    "number_code" => "428",
                    "phone_code" => "371",
                    "example" => "21 234 567"
                ],
                [
                    "name" => "Libyan Arab Jamahiriya",
                    "nickname" => "LIBYAN ARAB JAMAHIRIYA",
                    "iso2" => "LY",
                    "iso3" => "LBY",
                    "number_code" => "434",
                    "phone_code" => "218",
                    "example" => "091-2345678"
                ],
                [
                    "name" => "Morocco",
                    "nickname" => "MOROCCO",
                    "iso2" => "MA",
                    "iso3" => "MAR",
                    "number_code" => "504",
                    "phone_code" => "212",
                    "example" => "0650-123456"
                ],
                [
                    "name" => "Monaco",
                    "nickname" => "MONACO",
                    "iso2" => "MC",
                    "iso3" => "MCO",
                    "number_code" => "492",
                    "phone_code" => "377",
                    "example" => "06 12 34 56 78"
                ],
                [
                    "name" => "Moldova",
                    "nickname" => "MOLDOVA",
                    "iso2" => "MD",
                    "iso3" => "MDA",
                    "number_code" => "498",
                    "phone_code" => "373",
                    "example" => "0621 12 345"
                ],
                [
                    "name" => "Montenegro",
                    "nickname" => "MONTENEGRO",
                    "iso2" => "ME",
                    "iso3" => "MNE",
                    "number_code" => "499",
                    "phone_code" => "382",
                    "example" => "067 622 901"
                ],
                [
                    "name" => "Saint Martin",
                    "nickname" => "SAINT MARTIN",
                    "iso2" => "MF",
                    "iso3" => "MAF",
                    "number_code" => "663",
                    "phone_code" => "590",
                    "example" => "0690 00 12 34"
                ],
                [
                    "name" => "Madagascar",
                    "nickname" => "MADAGASCAR",
                    "iso2" => "MG",
                    "iso3" => "MDG",
                    "number_code" => "450",
                    "phone_code" => "261",
                    "example" => "032 12 345 67"
                ],
                [
                    "name" => "Marshall Islands",
                    "nickname" => "MARSHALL ISLANDS",
                    "iso2" => "MH",
                    "iso3" => "MHL",
                    "number_code" => "584",
                    "phone_code" => "692",
                    "example" => "235-1234"
                ],
                [
                    "name" => "Macedonia",
                    "nickname" => "MACEDONIA",
                    "iso2" => "MK",
                    "iso3" => "MKD",
                    "number_code" => "807",
                    "phone_code" => "389",
                    "example" => "072 345 678"
                ],
                [
                    "name" => "Mali",
                    "nickname" => "MALI",
                    "iso2" => "ML",
                    "iso3" => "MLI",
                    "number_code" => "466",
                    "phone_code" => "223",
                    "example" => "65 01 23 45"
                ],
                [
                    "name" => "Myanmar",
                    "nickname" => "MYANMAR",
                    "iso2" => "MM",
                    "iso3" => "MMR",
                    "number_code" => "104",
                    "phone_code" => "95",
                    "example" => "09 212 3456"
                ],
                [
                    "name" => "Mongolia",
                    "nickname" => "MONGOLIA",
                    "iso2" => "MN",
                    "iso3" => "MNG",
                    "number_code" => "496",
                    "phone_code" => "976",
                    "example" => "8812 3456"
                ],
                [
                    "name" => "Macao",
                    "nickname" => "MACAO",
                    "iso2" => "MO",
                    "iso3" => "MAC",
                    "number_code" => "446",
                    "phone_code" => "853",
                    "example" => "6612 3456"
                ],
                [
                    "name" => "Northern Mariana Islands",
                    "nickname" => "NORTHERN MARIANA ISLANDS",
                    "iso2" => "MP",
                    "iso3" => "MNP",
                    "number_code" => "580",
                    "phone_code" => "1",
                    "example" => "(670) 234-5678"
                ],
                [
                    "name" => "Martinique",
                    "nickname" => "MARTINIQUE",
                    "iso2" => "MQ",
                    "iso3" => "MTQ",
                    "number_code" => "474",
                    "phone_code" => "596",
                    "example" => "0696 20 12 34"
                ],
                [
                    "name" => "Mauritania",
                    "nickname" => "MAURITANIA",
                    "iso2" => "MR",
                    "iso3" => "MRT",
                    "number_code" => "478",
                    "phone_code" => "222",
                    "example" => "22 12 34 56"
                ],
                [
                    "name" => "Montserrat",
                    "nickname" => "MONTSERRAT",
                    "iso2" => "MS",
                    "iso3" => "MSR",
                    "number_code" => "500",
                    "phone_code" => "1",
                    "example" => "(664) 492-3456"
                ],
                [
                    "name" => "Malta",
                    "nickname" => "MALTA",
                    "iso2" => "MT",
                    "iso3" => "MLT",
                    "number_code" => "470",
                    "phone_code" => "356",
                    "example" => "9696 1234"
                ],
                [
                    "name" => "Mauritius",
                    "nickname" => "MAURITIUS",
                    "iso2" => "MU",
                    "iso3" => "MUS",
                    "number_code" => "480",
                    "phone_code" => "230",
                    "example" => "5251 2345"
                ],
                [
                    "name" => "Maldives",
                    "nickname" => "MALDIVES",
                    "iso2" => "MV",
                    "iso3" => "MDV",
                    "number_code" => "462",
                    "phone_code" => "960",
                    "example" => "771-2345"
                ],
                [
                    "name" => "Malawi",
                    "nickname" => "MALAWI",
                    "iso2" => "MW",
                    "iso3" => "MWI",
                    "number_code" => "454",
                    "phone_code" => "265",
                    "example" => "0991 23 45 67"
                ],
                [
                    "name" => "Mexico",
                    "nickname" => "MEXICO",
                    "iso2" => "MX",
                    "iso3" => "MEX",
                    "number_code" => "484",
                    "phone_code" => "52",
                    "example" => "222 123 4567"
                ],
                [
                    "name" => "Malaysia",
                    "nickname" => "MALAYSIA",
                    "iso2" => "MY",
                    "iso3" => "MYS",
                    "number_code" => "458",
                    "phone_code" => "60",
                    "example" => "012-345 6789"
                ],
                [
                    "name" => "Mozambique",
                    "nickname" => "MOZAMBIQUE",
                    "iso2" => "MZ",
                    "iso3" => "MOZ",
                    "number_code" => "508",
                    "phone_code" => "258",
                    "example" => "82 123 4567"
                ],
                [
                    "name" => "Namibia",
                    "nickname" => "NAMIBIA",
                    "iso2" => "NA",
                    "iso3" => "NAM",
                    "number_code" => "516",
                    "phone_code" => "264",
                    "example" => "081 123 4567"
                ],
                [
                    "name" => "New Caledonia",
                    "nickname" => "NEW CALEDONIA",
                    "iso2" => "NC",
                    "iso3" => "NCL",
                    "number_code" => "540",
                    "phone_code" => "687",
                    "example" => "75.12.34"
                ],
                [
                    "name" => "Niger",
                    "nickname" => "NIGER",
                    "iso2" => "NE",
                    "iso3" => "NER",
                    "number_code" => "562",
                    "phone_code" => "227",
                    "example" => "93 12 34 56"
                ],
                [
                    "name" => "Norfolk Island",
                    "nickname" => "NORFOLK ISLAND",
                    "iso2" => "NF",
                    "iso3" => "NFK",
                    "number_code" => "574",
                    "phone_code" => "672",
                    "example" => "3 81234"
                ],
                [
                    "name" => "Nigeria",
                    "nickname" => "NIGERIA",
                    "iso2" => "NG",
                    "iso3" => "NGA",
                    "number_code" => "566",
                    "phone_code" => "234",
                    "example" => "0802 123 4567"
                ],
                [
                    "name" => "Nicaragua",
                    "nickname" => "NICARAGUA",
                    "iso2" => "NI",
                    "iso3" => "NIC",
                    "number_code" => "558",
                    "phone_code" => "505",
                    "example" => "8123 4567"
                ],
                [
                    "name" => "Netherlands",
                    "nickname" => "NETHERLANDS",
                    "iso2" => "NL",
                    "iso3" => "NLD",
                    "number_code" => "528",
                    "phone_code" => "31",
                    "example" => "06 12345678"
                ],
                [
                    "name" => "Norway",
                    "nickname" => "NORWAY",
                    "iso2" => "NO",
                    "iso3" => "NOR",
                    "number_code" => "578",
                    "phone_code" => "47",
                    "example" => "40 61 23 45"
                ],
                [
                    "name" => "Nepal",
                    "nickname" => "NEPAL",
                    "iso2" => "NP",
                    "iso3" => "NPL",
                    "number_code" => "524",
                    "phone_code" => "977",
                    "example" => "984-1234567"
                ],
                [
                    "name" => "Nauru",
                    "nickname" => "NAURU",
                    "iso2" => "NR",
                    "iso3" => "NRU",
                    "number_code" => "520",
                    "phone_code" => "674",
                    "example" => "555 1234"
                ],
                [
                    "name" => "Niue",
                    "nickname" => "NIUE",
                    "iso2" => "NU",
                    "iso3" => "NIU",
                    "number_code" => "570",
                    "phone_code" => "683",
                    "example" => "888 4012"
                ],
                [
                    "name" => "New Zealand",
                    "nickname" => "NEW ZEALAND",
                    "iso2" => "NZ",
                    "iso3" => "NZL",
                    "number_code" => "554",
                    "phone_code" => "64",
                    "example" => "021 123 4567"
                ],
                [
                    "name" => "Oman",
                    "nickname" => "OMAN",
                    "iso2" => "OM",
                    "iso3" => "OMN",
                    "number_code" => "512",
                    "phone_code" => "968",
                    "example" => "9212 3456"
                ],
                [
                    "name" => "Panama",
                    "nickname" => "PANAMA",
                    "iso2" => "PA",
                    "iso3" => "PAN",
                    "number_code" => "591",
                    "phone_code" => "507",
                    "example" => "6123-4567"
                ],
                [
                    "name" => "Peru",
                    "nickname" => "PERU",
                    "iso2" => "PE",
                    "iso3" => "PER",
                    "number_code" => "604",
                    "phone_code" => "51",
                    "example" => "912 345 678"
                ],
                [
                    "name" => "French Polynesia",
                    "nickname" => "FRENCH POLYNESIA",
                    "iso2" => "PF",
                    "iso3" => "PYF",
                    "number_code" => "258",
                    "phone_code" => "689",
                    "example" => "87 12 34 56"
                ],
                [
                    "name" => "Papua New Guinea",
                    "nickname" => "PAPUA NEW GUINEA",
                    "iso2" => "PG",
                    "iso3" => "PNG",
                    "number_code" => "598",
                    "phone_code" => "675",
                    "example" => "7012 3456"
                ],
                [
                    "name" => "Philippines",
                    "nickname" => "PHILIPPINES",
                    "iso2" => "PH",
                    "iso3" => "PHL",
                    "number_code" => "608",
                    "phone_code" => "63",
                    "example" => "0905 123 4567"
                ],
                [
                    "name" => "Pakistan",
                    "nickname" => "PAKISTAN",
                    "iso2" => "PK",
                    "iso3" => "PAK",
                    "number_code" => "586",
                    "phone_code" => "92",
                    "example" => "0301 2345678"
                ],
                [
                    "name" => "Poland",
                    "nickname" => "POLAND",
                    "iso2" => "PL",
                    "iso3" => "POL",
                    "number_code" => "616",
                    "phone_code" => "48",
                    "example" => "512 345 678"
                ],
                [
                    "name" => "Saint Pierre And Miquelon",
                    "nickname" => "SAINT PIERRE AND MIQUELON",
                    "iso2" => "PM",
                    "iso3" => "SPM",
                    "number_code" => "666",
                    "phone_code" => "508",
                    "example" => "055 12 34"
                ],
                [
                    "name" => "Puerto Rico",
                    "nickname" => "PUERTO RICO",
                    "iso2" => "PR",
                    "iso3" => "PRI",
                    "number_code" => "630",
                    "phone_code" => "1",
                    "example" => "(787) 234-5678"
                ],
                [
                    "name" => "Palestinian Territory, Occupied",
                    "nickname" => "PALESTINIAN TERRITORY, OCCUPIED",
                    "iso2" => "PS",
                    "iso3" => "PSE",
                    "number_code" => "275",
                    "phone_code" => "970",
                    "example" => "0599 123 456"
                ],
                [
                    "name" => "Portugal",
                    "nickname" => "PORTUGAL",
                    "iso2" => "PT",
                    "iso3" => "PRT",
                    "number_code" => "620",
                    "phone_code" => "351",
                    "example" => "912 345 678"
                ],
                [
                    "name" => "Palau",
                    "nickname" => "PALAU",
                    "iso2" => "PW",
                    "iso3" => "PLW",
                    "number_code" => "585",
                    "phone_code" => "680",
                    "example" => "620 1234"
                ],
                [
                    "name" => "Paraguay",
                    "nickname" => "PARAGUAY",
                    "iso2" => "PY",
                    "iso3" => "PRY",
                    "number_code" => "600",
                    "phone_code" => "595",
                    "example" => "0961 456789"
                ],
                [
                    "name" => "Qatar",
                    "nickname" => "QATAR",
                    "iso2" => "QA",
                    "iso3" => "QAT",
                    "number_code" => "634",
                    "phone_code" => "974",
                    "example" => "3312 3456"
                ],
                [
                    "name" => "Reunion",
                    "nickname" => "REUNION",
                    "iso2" => "RE",
                    "iso3" => "REU",
                    "number_code" => "638",
                    "phone_code" => "262",
                    "example" => "0692 12 34 56"
                ],
                [
                    "name" => "Romania",
                    "nickname" => "ROMANIA",
                    "iso2" => "RO",
                    "iso3" => "ROU",
                    "number_code" => "642",
                    "phone_code" => "40",
                    "example" => "0712 034 567"
                ],
                [
                    "name" => "Serbia",
                    "nickname" => "SERBIA",
                    "iso2" => "RS",
                    "iso3" => "SRB",
                    "number_code" => "688",
                    "phone_code" => "381",
                    "example" => "060 1234567"
                ],
                [
                    "name" => "Russian Federation",
                    "nickname" => "RUSSIAN FEDERATION",
                    "iso2" => "RU",
                    "iso3" => "RUS",
                    "number_code" => "643",
                    "phone_code" => "7",
                    "example" => "8 (912) 345-67-89"
                ],
                [
                    "name" => "Rwanda",
                    "nickname" => "RWANDA",
                    "iso2" => "RW",
                    "iso3" => "RWA",
                    "number_code" => "646",
                    "phone_code" => "250",
                    "example" => "0720 123 456"
                ],
                [
                    "name" => "Saudi Arabia",
                    "nickname" => "SAUDI ARABIA",
                    "iso2" => "SA",
                    "iso3" => "SAU",
                    "number_code" => "682",
                    "phone_code" => "966",
                    "example" => "051 234 5678"
                ],
                [
                    "name" => "Solomon Islands",
                    "nickname" => "SOLOMON ISLANDS",
                    "iso2" => "SB",
                    "iso3" => "SLB",
                    "number_code" => "090",
                    "phone_code" => "677",
                    "example" => "74 21234"
                ],
                [
                    "name" => "Seychelles",
                    "nickname" => "SEYCHELLES",
                    "iso2" => "SC",
                    "iso3" => "SYC",
                    "number_code" => "690",
                    "phone_code" => "248",
                    "example" => "2 510 123"
                ],
                [
                    "name" => "Sudan",
                    "nickname" => "SUDAN",
                    "iso2" => "SD",
                    "iso3" => "SDN",
                    "number_code" => "736",
                    "phone_code" => "249",
                    "example" => "091 123 1234"
                ],
                [
                    "name" => "Sweden",
                    "nickname" => "SWEDEN",
                    "iso2" => "SE",
                    "iso3" => "SWE",
                    "number_code" => "752",
                    "phone_code" => "46",
                    "example" => "070-123 45 67"
                ],
                [
                    "name" => "Singapore",
                    "nickname" => "SINGAPORE",
                    "iso2" => "SG",
                    "iso3" => "SGP",
                    "number_code" => "702",
                    "phone_code" => "65",
                    "example" => "8123 4567"
                ],
                [
                    "name" => "Saint Helena",
                    "nickname" => "SAINT HELENA",
                    "iso2" => "SH",
                    "iso3" => "SHN",
                    "number_code" => "654",
                    "phone_code" => "290",
                    "example" => "51234"
                ],
                [
                    "name" => "Slovenia",
                    "nickname" => "SLOVENIA",
                    "iso2" => "SI",
                    "iso3" => "SVN",
                    "number_code" => "705",
                    "phone_code" => "386",
                    "example" => "031 234 567"
                ],
                [
                    "name" => "Svalbard And Jan Mayen",
                    "nickname" => "SVALBARD AND JAN MAYEN",
                    "iso2" => "SJ",
                    "iso3" => "SJM",
                    "number_code" => "744",
                    "phone_code" => "47",
                    "example" => "41 23 45 67"
                ],
                [
                    "name" => "Slovakia",
                    "nickname" => "SLOVAKIA",
                    "iso2" => "SK",
                    "iso3" => "SVK",
                    "number_code" => "703",
                    "phone_code" => "421",
                    "example" => "0912 123 456"
                ],
                [
                    "name" => "Sierra Leone",
                    "nickname" => "SIERRA LEONE",
                    "iso2" => "SL",
                    "iso3" => "SLE",
                    "number_code" => "694",
                    "phone_code" => "232",
                    "example" => "(025) 123456"
                ],
                [
                    "name" => "San Marino",
                    "nickname" => "SAN MARINO",
                    "iso2" => "SM",
                    "iso3" => "SMR",
                    "number_code" => "674",
                    "phone_code" => "378",
                    "example" => "66 66 12 12"
                ],
                [
                    "name" => "Senegal",
                    "nickname" => "SENEGAL",
                    "iso2" => "SN",
                    "iso3" => "SEN",
                    "number_code" => "686",
                    "phone_code" => "221",
                    "example" => "70 123 45 67"
                ],
                [
                    "name" => "Somalia",
                    "nickname" => "SOMALIA",
                    "iso2" => "SO",
                    "iso3" => "SOM",
                    "number_code" => "706",
                    "phone_code" => "252",
                    "example" => "7 1123456"
                ],
                [
                    "name" => "Suriname",
                    "nickname" => "SURINAME",
                    "iso2" => "SR",
                    "iso3" => "SUR",
                    "number_code" => "740",
                    "phone_code" => "597",
                    "example" => "741-2345"
                ],
                [
                    "name" => "South Sudan",
                    "nickname" => "SOUTH SUDAN",
                    "iso2" => "SS",
                    "iso3" => "SSD",
                    "number_code" => "728",
                    "phone_code" => "211",
                    "example" => "0977 123 456"
                ],
                [
                    "name" => "Sao Tome And Principe",
                    "nickname" => "SAO TOME AND PRINCIPE",
                    "iso2" => "ST",
                    "iso3" => "STP",
                    "number_code" => "678",
                    "phone_code" => "239",
                    "example" => "981 2345"
                ],
                [
                    "name" => "El Salvador",
                    "nickname" => "EL SALVADOR",
                    "iso2" => "SV",
                    "iso3" => "SLV",
                    "number_code" => "222",
                    "phone_code" => "503",
                    "example" => "7012 3456"
                ],
                [
                    "name" => "Sint Maarten",
                    "nickname" => "SINT MAARTEN",
                    "iso2" => "SX",
                    "iso3" => "SXM",
                    "number_code" => "534",
                    "phone_code" => "1",
                    "example" => "(721) 520-5678"
                ],
                [
                    "name" => "Syrian Arab Republic",
                    "nickname" => "SYRIAN ARAB REPUBLIC",
                    "iso2" => "SY",
                    "iso3" => "SYR",
                    "number_code" => "760",
                    "phone_code" => "963",
                    "example" => "0944 567 890"
                ],
                [
                    "name" => "Swaziland",
                    "nickname" => "SWAZILAND",
                    "iso2" => "SZ",
                    "iso3" => "SWZ",
                    "number_code" => "748",
                    "phone_code" => "268",
                    "example" => "7612 3456"
                ],
                [
                    "name" => "Tristan da Cunha",
                    "nickname" => "TRISTAN DA CUNHA",
                    "iso2" => "TA",
                    "iso3" => "SHP",
                    "number_code" => "654",
                    "phone_code" => "290",
                    "example" => "8999"
                ],
                [
                    "name" => "Turks And Caicos Islands",
                    "nickname" => "TURKS AND CAICOS ISLANDS",
                    "iso2" => "TC",
                    "iso3" => "TCA",
                    "number_code" => "796",
                    "phone_code" => "1",
                    "example" => "(649) 231-1234"
                ],
                [
                    "name" => "Chad",
                    "nickname" => "CHAD",
                    "iso2" => "TD",
                    "iso3" => "TCD",
                    "number_code" => "148",
                    "phone_code" => "235",
                    "example" => "63 01 23 45"
                ],
                [
                    "name" => "Togo",
                    "nickname" => "TOGO",
                    "iso2" => "TG",
                    "iso3" => "TGO",
                    "number_code" => "768",
                    "phone_code" => "228",
                    "example" => "90 11 23 45"
                ],
                [
                    "name" => "Thailand",
                    "nickname" => "THAILAND",
                    "iso2" => "TH",
                    "iso3" => "THA",
                    "number_code" => "764",
                    "phone_code" => "66",
                    "example" => "081 234 5678"
                ],
                [
                    "name" => "Tajikistan",
                    "nickname" => "TAJIKISTAN",
                    "iso2" => "TJ",
                    "iso3" => "TJK",
                    "number_code" => "762",
                    "phone_code" => "992",
                    "example" => "91 712 3456"
                ],
                [
                    "name" => "Tokelau",
                    "nickname" => "TOKELAU",
                    "iso2" => "TK",
                    "iso3" => "TKL",
                    "number_code" => "772",
                    "phone_code" => "690",
                    "example" => "7290"
                ],
                [
                    "name" => "Timor-Leste",
                    "nickname" => "TIMOR-LESTE",
                    "iso2" => "TL",
                    "iso3" => "TLS",
                    "number_code" => "626",
                    "phone_code" => "670",
                    "example" => "7721 2345"
                ],
                [
                    "name" => "Turkmenistan",
                    "nickname" => "TURKMENISTAN",
                    "iso2" => "TM",
                    "iso3" => "TKM",
                    "number_code" => "795",
                    "phone_code" => "993",
                    "example" => "8 66 123456"
                ],
                [
                    "name" => "Tunisia",
                    "nickname" => "TUNISIA",
                    "iso2" => "TN",
                    "iso3" => "TUN",
                    "number_code" => "788",
                    "phone_code" => "216",
                    "example" => "20 123 456"
                ],
                [
                    "name" => "Tonga",
                    "nickname" => "TONGA",
                    "iso2" => "TO",
                    "iso3" => "TON",
                    "number_code" => "776",
                    "phone_code" => "676",
                    "example" => "771 5123"
                ],
                [
                    "name" => "Turkey",
                    "nickname" => "TURKEY",
                    "iso2" => "TR",
                    "iso3" => "TUR",
                    "number_code" => "792",
                    "phone_code" => "90",
                    "example" => "0501 234 56 78"
                ],
                [
                    "name" => "Trinidad And Tobago",
                    "nickname" => "TRINIDAD AND TOBAGO",
                    "iso2" => "TT",
                    "iso3" => "TTO",
                    "number_code" => "780",
                    "phone_code" => "1",
                    "example" => "(868) 291-1234"
                ],
                [
                    "name" => "Tuvalu",
                    "nickname" => "TUVALU",
                    "iso2" => "TV",
                    "iso3" => "TUV",
                    "number_code" => "798",
                    "phone_code" => "688",
                    "example" => "90 1234"
                ],
                [
                    "name" => "Taiwan",
                    "nickname" => "TAIWAN",
                    "iso2" => "TW",
                    "iso3" => "TWN",
                    "number_code" => "158",
                    "phone_code" => "886",
                    "example" => "0912 345 678"
                ],
                [
                    "name" => "Tanzania",
                    "nickname" => "TANZANIA",
                    "iso2" => "TZ",
                    "iso3" => "TZA",
                    "number_code" => "834",
                    "phone_code" => "255",
                    "example" => "0621 234 567"
                ],
                [
                    "name" => "Ukraine",
                    "nickname" => "UKRAINE",
                    "iso2" => "UA",
                    "iso3" => "UKR",
                    "number_code" => "804",
                    "phone_code" => "380",
                    "example" => "050 123 4567"
                ],
                [
                    "name" => "Uganda",
                    "nickname" => "UGANDA",
                    "iso2" => "UG",
                    "iso3" => "UGA",
                    "number_code" => "800",
                    "phone_code" => "256",
                    "example" => "0712 345678"
                ],
                [
                    "name" => "United States",
                    "nickname" => "UNITED STATES",
                    "iso2" => "US",
                    "iso3" => "USA",
                    "number_code" => "840",
                    "phone_code" => "1",
                    "example" => "(201) 555-0123"
                ],
                [
                    "name" => "Uruguay",
                    "nickname" => "URUGUAY",
                    "iso2" => "UY",
                    "iso3" => "URY",
                    "number_code" => "858",
                    "phone_code" => "598",
                    "example" => "094 231 234"
                ],
                [
                    "name" => "Uzbekistan",
                    "nickname" => "UZBEKISTAN",
                    "iso2" => "UZ",
                    "iso3" => "UZB",
                    "number_code" => "860",
                    "phone_code" => "998",
                    "example" => "91 234 56 78"
                ],
                [
                    "name" => "Holy See (Vatican City State)",
                    "nickname" => "HOLY SEE (VATICAN CITY STATE)",
                    "iso2" => "VA",
                    "iso3" => "VAT",
                    "number_code" => "336",
                    "phone_code" => "39",
                    "example" => "312 345 6789"
                ],
                [
                    "name" => "Saint Vincent And Grenadines",
                    "nickname" => "SAINT VINCENT AND GRENADINES",
                    "iso2" => "VC",
                    "iso3" => "VCT",
                    "number_code" => "670",
                    "phone_code" => "1",
                    "example" => "(784) 430-1234"
                ],
                [
                    "name" => "Venezuela",
                    "nickname" => "VENEZUELA",
                    "iso2" => "VE",
                    "iso3" => "VEN",
                    "number_code" => "862",
                    "phone_code" => "58",
                    "example" => "0412-1234567"
                ],
                [
                    "name" => "Virgin Islands, British",
                    "nickname" => "VIRGIN ISLANDS, BRITISH",
                    "iso2" => "VG",
                    "iso3" => "VGB",
                    "number_code" => "092",
                    "phone_code" => "1",
                    "example" => "(284) 300-1234"
                ],
                [
                    "name" => "Virgin Islands, U.S.",
                    "nickname" => "VIRGIN ISLANDS, U.S.",
                    "iso2" => "VI",
                    "iso3" => "VIR",
                    "number_code" => "850",
                    "phone_code" => "1",
                    "example" => "(340) 642-1234"
                ],
                [
                    "name" => "Vietnam",
                    "nickname" => "VIETNAM",
                    "iso2" => "VN",
                    "iso3" => "VNM",
                    "number_code" => "704",
                    "phone_code" => "84",
                    "example" => "0912 345 678"
                ],
                [
                    "name" => "Vanuatu",
                    "nickname" => "VANUATU",
                    "iso2" => "VU",
                    "iso3" => "VUT",
                    "number_code" => "548",
                    "phone_code" => "678",
                    "example" => "591 2345"
                ],
                [
                    "name" => "Wallis And Futuna",
                    "nickname" => "WALLIS AND FUTUNA",
                    "iso2" => "WF",
                    "iso3" => "WLF",
                    "number_code" => "876",
                    "phone_code" => "681",
                    "example" => "82 12 34"
                ],
                [
                    "name" => "Samoa",
                    "nickname" => "SAMOA",
                    "iso2" => "WS",
                    "iso3" => "WSM",
                    "number_code" => "882",
                    "phone_code" => "685",
                    "example" => "72 12345"
                ],
                [
                    "name" => "Kosovo",
                    "nickname" => "KOSOVO",
                    "iso2" => "XK",
                    "iso3" => "KOS",
                    "number_code" => "382",
                    "phone_code" => "383",
                    "example" => "043 201 234"
                ],
                [
                    "name" => "Yemen",
                    "nickname" => "YEMEN",
                    "iso2" => "YE",
                    "iso3" => "YEM",
                    "number_code" => "887",
                    "phone_code" => "967",
                    "example" => "0712 345 678"
                ],
                [
                    "name" => "Mayotte",
                    "nickname" => "MAYOTTE",
                    "iso2" => "YT",
                    "iso3" => "MYT",
                    "number_code" => "175",
                    "phone_code" => "262",
                    "example" => "0639 01 23 45"
                ],
                [
                    "name" => "South Africa",
                    "nickname" => "SOUTH AFRICA",
                    "iso2" => "ZA",
                    "iso3" => "ZAF",
                    "number_code" => "710",
                    "phone_code" => "27",
                    "example" => "071 123 4567"
                ],
                [
                    "name" => "Zambia",
                    "nickname" => "ZAMBIA",
                    "iso2" => "ZM",
                    "iso3" => "ZMB",
                    "number_code" => "894",
                    "phone_code" => "260",
                    "example" => "095 5123456"
                ],
                [
                    "name" => "Zimbabwe",
                    "nickname" => "ZIMBABWE",
                    "iso2" => "ZW",
                    "iso3" => "ZWE",
                    "number_code" => "716",
                    "phone_code" => "263",
                    "example" => "071 234 5678"
                ]
            ];
        foreach ($countryCodes as $country) {
            CountryCode::updateOrCreate(
                ['name' => $country['name']],
                [
                    'nickname' => $country['nickname'],
                    'iso2' => $country['iso2'],
                    'iso3' => $country['iso3'],
                    'number_code' => $country['number_code'],
                    'phone_code' => $country['phone_code'],
                    'example' => $country['example']
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
            'UTC' => '(GMT+00:00) UTC',
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
            'timezone' => Timezone::where('name','UTC')->value('id'),
            'date_format' => DateFormat::where('format','F j, Y')->value('id'),
            'time_format' => TimeFormat::where('format','g:i a')->value('id'),
        ];
        foreach ($settings as $key => $value) {
            CommonSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
