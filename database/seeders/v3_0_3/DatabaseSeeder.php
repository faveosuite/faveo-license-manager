<?php

namespace Database\Seeders\v3_0_3;

use App\Models\CommonSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commonSettingSeeder();
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
}
