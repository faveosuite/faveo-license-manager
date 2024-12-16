<?php

namespace App\Console\Commands;

use App\Http\Controllers\Installer\InstallerController;
use App\Http\Controllers\SyncLicenseToLatestVersion;
use App\Models\AflClients;
use DB;
use Illuminate\Console\Command;

class InstallDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'installinf database';

    protected $install;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->install = new InstallerController();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $env = base_path().DIRECTORY_SEPARATOR.'.env';
            if (! is_file($env)) {
                throw new \Exception("Please run 'php artisan install:license'");
            }
            if ($this->confirm('Do you want to migrate tables now?')) {
                $this->call('key:generate', ['--force' => true]);
                $this->checkDBVersion();
                $this->call('passport:install', ['--force' => true]);
                (new SyncLicenseToLatestVersion)->sync();

                $headers = ['email', 'password'];
                $data = [
                    [

                        'email' => 'demo@gmail.com',
                        'password' => 'demopass',
                    ],
                ];
                $user = new AflClients([

                    'client_email' => 'demo@gmail.com',
                    'client_password' => \Hash::make('demopass'),
                    'client_role' => 'admin'

                ]);
                $user->save();
                $this->install->updateInstalEnv($env);
                $this->table($headers, $data);
                $this->warn('Please update your email and change the password immediately');
                $url = \Config::get('app.url');
                $this->info("License has been installed successfully. Please visit $url to login");
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }
    }

    private function checkDBVersion(): void
    {
        try {
            DB::purge('mysql'); // Ensure Laravel resets connection settings
            $pdo = DB::connection()->getPdo();
            $version = $pdo->query('select version()')->fetchColumn();

            if (strpos($version, 'Maria') === false) {
                $this->checkMySQLVersion($version);
                return;
            }

            $this->checkMariaDBVersion($version);
        } catch (\Exception $e) {
            if ($e->getCode() != 1049) { // 1049: Unknown database error
                throw $e;
            }

            $database = config('database.connections.mysql.database');

            // Temporarily set database to null to allow DB creation
            config(['database.connections.mysql.database' => null]);
            DB::purge('mysql'); // Purge the connection before creating DB

            createDB($database); // Create the database

            config(['database.connections.mysql.database' => $database]);
            DB::reconnect(); // Reconnect to the new database

            $this->checkDBVersion(); // Retry the check
        }
    }

    /**
     * Function to check version requirement for MariaDB
     *
     * @param  string  $version
     * @return void
     */
    private function checkMariaDBVersion(string $version): void
    {
        $this->compareVersion($this->printAndFormatVersion($version, 'MariaDB'), '10.3', 'MariaDB');
    }

    /**
     * Function to check version requirement for MySQL
     *
     * @param  string  $version
     * @return void
     */
    private function checkMySQLVersion(string $version): void
    {
        $this->compareVersion($this->printAndFormatVersion($version, 'MySQL'), '5.6', 'MySQL');
    }

    /**
     * Function compares database version with minimum required version
     *
     * @param  string  $version  unfomatted version string
     * @param  string  $min      minimum required version for database
     * @param  string  $db       database name
     * @return  void
     *
     * @throws  Exception
     */
    private function compareVersion($version, $min, $db = 'MySQL'): void
    {
        if (version_compare($version, $min) < 0) {
            throw new \Exception("Please update your $db database version to $min or greater");
        }
    }

    /**
     * Function prints database version and returns formatted version string
     *
     * @param  string  $version  unfomatted version string
     * @param  string  $db       database name
     * @return  string            formatted version string
     */
    private function printAndFormatVersion(string $version, string $db = 'MySQL'): string
    {
        $this->info("You are running $db database on version $version");
        preg_match("/^[0-9\.]+/", $version, $match);

        return $match[0];
    }
}
