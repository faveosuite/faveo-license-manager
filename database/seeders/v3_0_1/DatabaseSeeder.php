<?php

namespace Database\Seeders\v3_0_1;


use App\Models\AflSettings;
use App\Models\ScheduleCron;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder{

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->seedEmail();
        $this->cronsTable();
        $this->settingsTable();
    }

    private function seedEmail(){

        $this->insertEnvKeysIfNotPresent();
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
}
