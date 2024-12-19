<?php

namespace Database\Seeders\v3_0_2_1;

use App\Models\AflLicenseSchemes;
use App\Models\CommonSetting;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commonSettingSeeder();
        $this->insertNewSchemes();
    }
    private function commonSettingSeeder()
    {
        $settings = [
            'license_app_key' => '',
            'license_app_secret' => '',
        ];
        foreach ($settings as $key => $value) {
            CommonSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    private function insertNewSchemes(){

        $schemes = [
            [
                'scheme_query' => "CREATE TABLE %APL_PLUGIN_DATABASE_TABLE% (
                SETTING_ID TINYINT(1) NOT NULL AUTO_INCREMENT,
                ROOT_URL VARCHAR(250) NOT NULL,
                CLIENT_EMAIL VARCHAR(250) NOT NULL,
                LICENSE_CODE VARCHAR(250) NOT NULL,
                LCD VARCHAR(250) NOT NULL,
                LRD VARCHAR(250) NOT NULL,
                INSTALLATION_KEY VARCHAR(250) NOT NULL,
                INSTALLATION_HASH VARCHAR(250) NOT NULL,
                PLUGIN_NAME VARCHAR(250) NOT NULL,
                PRIMARY KEY (SETTING_ID)
            ) DEFAULT CHARSET=utf8;

            INSERT INTO %APL_PLUGIN_DATABASE_TABLE% (ROOT_URL, CLIENT_EMAIL, LICENSE_CODE, LCD, LRD, INSTALLATION_KEY, INSTALLATION_HASH, PLUGIN_NAME)
            VALUES ('%ROOT_URL%', '%CLIENT_EMAIL%', '%LICENSE_CODE%', '%LCD%', '%LRD%', '%INSTALLATION_KEY%', '%INSTALLATION_HASH%', '%PLUGIN_NAME%');",
                'scheme_status' => 1,
                'id' => 2  // Added scheme_name
            ],
            [
                'scheme_query' => "INSERT INTO %APL_PLUGIN_DATABASE_TABLE% (ROOT_URL, CLIENT_EMAIL, LICENSE_CODE, LCD, LRD, INSTALLATION_KEY, INSTALLATION_HASH, PLUGIN_NAME)
            VALUES ('%ROOT_URL%', '%CLIENT_EMAIL%', '%LICENSE_CODE%', '%LCD%', '%LRD%', '%INSTALLATION_KEY%', '%INSTALLATION_HASH%', '%PLUGIN_NAME%');",
                'scheme_status' => 1,
                'id' => 3  // Added scheme_name
            ]
        ];

        foreach ($schemes as $scheme) {
            AflLicenseSchemes::updateOrcreate(['id' => $scheme['id']], $scheme);
        }
    }
}
