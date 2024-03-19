<?php

namespace Database\Seeders\v3_0_1;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AflSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder{

    /**
     * Run the database seeds.
     */
    public function run()
    {
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
