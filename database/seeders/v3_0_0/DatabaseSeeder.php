<?php
namespace Database\Seeders\v3_0_0;

use App\Models\AflClients;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AflSettings;

class DatabaseSeeder extends \Database\Seeders\DatabaseSeeder{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Transfer data from admins table to users table
        $adminsData = DB::table('afl_admins')->select([
            'admin_fname as client_fname',
            'admin_lname as client_lname',
            'admin_email as client_email',
            'admin_password as client_password',
            'admin_status as client_status',
            //'created_at as client_active_date',
        ])->get();

        foreach ($adminsData as $admin) {
            AflClients::updateOrCreate([
                'client_fname' => $admin->client_fname,
                'client_lname' => $admin->client_lname,
                'client_email' => $admin->client_email,
                'client_password' => $admin->client_password,
                'client_status' => $admin->client_status,
                //'client_active_date' => $admin->client_active_date,
                'client_role'=> 'admin',
            ]);
        }
        $this->settings();
    }
    public function settings()
    {
        AflSettings::where('SETTING_ID', 1)->update([
            'NEWS_TEXT' => '[{"title":"license manager Scripts Receive New Features and Core Updates","full_url":"https:\/\/www.license manager.com\/blog\/license manager-scripts-receive-new-features-and-core-updates\/","date":"2019-08-19"},{"title":"Force User Input Validation with Auto PHP Licenser 2.5","full_url":"https:\/\/www.license manager.com\/blog\/force-user-input-validation-with-auto-php-licenser-2-5\/","date":"2019-04-18"},{"title":"Dead Man Switch 1.4 Gets Visual Emails Composer","full_url":"https:\/\/www.license manager.com\/blog\/dead-man-switch-1-4-gets-visual-emails-composer\/","date":"2019-01-28"}]',
        ]);
    }
}
