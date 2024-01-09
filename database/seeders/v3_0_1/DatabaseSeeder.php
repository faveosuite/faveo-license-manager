<?php

namespace Database\Seeders\v3_0_1;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AflSettings;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder{

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $originalConfigKeys = [
            'MAIL_DRIVER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_USERNAME',
        ];
        $configValues = AflSettings::first();
        $configValues->EMAIL_DRIVER = env('MAIL_DRIVER' , '');
        $configValues->EMAIL_HOST = env('MAIL_HOST' , '');
        $configValues->EMAIL_PORT = env('MAIL_PORT' , null);
        $configValues->EMAIL_PASSWORD = env('MAIL_PASSWORD' , '');
        $configValues->EMAIL_FROM_NAME = env('MAIL_USERNAME', '');
        $configValues->EMAIL_ENCRYPTION = env('MAIL_ENCRYPTION', '');
        $configValues->save();
        
        foreach ($originalConfigKeys as $key) {
            putenv($key);
        }

    }
}
