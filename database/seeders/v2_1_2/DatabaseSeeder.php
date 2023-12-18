<?php
namespace Database\Seeders\v2_1_2;

use App\Models\AflClients;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
    }
}
