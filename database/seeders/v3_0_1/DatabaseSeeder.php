<?php

namespace Database\Seeders\v3_0_1;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AflSettings;
use Illuminate\Support\Facades\DB;
use App\Models\ScheduleCron;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder{

    /**
     * Run the database seeds.
     */
    public function run()
    {

        $this->seedEmail();
        $this->cronsTable();
    }

    private function seedEmail(){

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

    public function cronsTable()
    {
        ScheduleCron::insert([
        ['scenario' => 'callback-cleanup', 'value' => 'everyMinute', 'command' => 'app:crack-callback-cleanup', 'status' => 1, 'icon' => 'glyphicon glyphicon-random' , 'job_info' => 'callback_cleanup_tooltip' , 'created_at' => now(), 'updated_at' => now()],
        ['scenario' => 'crack-reports-cleanup', 'value' => 'everyMinute', 'command' => 'app:crack-reports-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-modal-window' , 'job_info' => 'crack_cleanup_tooltip' , 'created_at' => now(), 'updated_at' => now()],
        ['scenario' => 'license-reports-cleanup', 'value' => 'everyMinute', 'command' => 'app:license-reports-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-tags' , 'job_info' => 'license_report_cleanup_tooltip' ,'created_at' => now(), 'updated_at' => now()],
        ['scenario' => 'system-reports-cleanup', 'value' => 'everyMinute', 'command' => 'app:system-reports-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-wrench' , 'job_info' => 'system_cleanup_tooltip' ,'created_at' => now(), 'updated_at' => now()],
        ['scenario' => 'versions-cleanup', 'value' => 'everyMinute', 'command' => 'app:versions-cleanup', 'status' => 1,  'icon' => 'glyphicon glyphicon-refresh' , 'job_info' => 'version_cleanup_tooltip' ,'created_at' => now(), 'updated_at' => now()],
        ]);
    }

}
