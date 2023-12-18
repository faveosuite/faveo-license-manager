<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Models\AflSettings;
use Illuminate\Support\Facades\File;



use Exception;

class SyncLicenseToLatestVersion extends Controller
{
    private $log = '';

    public function sync()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        // in case where isInstall is false(in case of new install) version number should be zero
        $latestVersion = $this->getPHPCompatibleVersionString(config('app.version'));
        $olderVersion = $this->getOlderVersion();

        try {
            $this->updateToLatestVersion($latestVersion, $olderVersion);
            $this->cacheDbVersion();
            $this->clearViewCache();
            $this->clearConfig();
            $this->setDBInstall(1);
            AflSettings::first()->update(['DATABASE_VERSION'=> 'v'.$latestVersion]);

             $this->cacheDbVersion();
        } catch (Exception $ex) {
            if (! $this->isInstall()) {
                //if system is not installed chances are logs tables are not present
                throw $ex;
            }

            $this->log = $this->log."\n".$ex->getMessage();
        }

        return $this->log;
    }

     private function writeToEnvAndRunConfigClear($key, $value)
     {
         try {
             $path = app()->environmentFilePath();

             $escaped = preg_quote('='.env($key), '/');
             file_put_contents($path, preg_replace(
                 "/^{$key}{$escaped}/m",
                 "{$key}={$value}",
                 file_get_contents($path)
             ));
             Artisan::call('config:clear');
         } catch (Exception $e) {
             return errorResponse($e->getMessage());
         }
     }

    private function cacheDbVersion()
    {
        $filesystemVersion = Config::get('app.version');
        Cache::forget($filesystemVersion);
        $dbversion = Cache::remember($filesystemVersion, 3600, function () { //Caching version for 1 hr
        return AflSettings::first()->value('DATABASE_VERSION');
        });
    }

    private function getPHPCompatibleVersionString(string $version): string
    {
        return preg_replace('#v\.|v#', '', str_replace('_', '.', $version));
    }

    private function getOlderVersion(): string
    {
      if (! $this->isInstall()) {
          return $this->getPHPCompatibleVersionString('v0.0.0');
        }
        $olderVersion = DB::table('afl_settings')->value('DATABASE_VERSION');
        $olderVersion = $olderVersion ? $olderVersion : 'v0.0.0';

        return $this->getPHPCompatibleVersionString($olderVersion);
    }

    public function updateToLatestVersion(string $latestVersion, string $olderVersion)
    {
        $this->updateMigrationTable($olderVersion);

        // after older version is updated, update to the latest version in which seeder versioning is implemented
        Artisan::call('migrate', ['--force' => true]);

        $this->handleArtisanLogs();

        // getting seeder base path
        $seederBasePath = base_path().DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'seeders';

        // get all directories inside seeder folder
        // sort versions from oldest to latest
        if (file_exists($seederBasePath)) {
            $seederVersions = scandir($seederBasePath);
            natsort($seederVersions);
            // convert older and newer version into underscore format
            $formattedOlderVersion = $olderVersion;
            foreach ($seederVersions as $version) {
    // Compare versions using a PHP-compatible version string
    if (version_compare($this->getPHPCompatibleVersionString($version), $formattedOlderVersion) == 1) {
        // Dynamically construct the seeder class name based on the version
        $seederClassName = "Database\\Seeders\\$version\\DatabaseSeeder";

        // Run migration and seeding for the version
        $this->log = $this->log . "\n" . "Running Seeder for version $version";
        Artisan::call('migrate', ['--path' => 'database/migrations', '--force' => true]);
        Artisan::call('db:seed', ['--class' => $seederClassName, '--force' => true]);
        $this->handleArtisanLogs();
    }
}
        }
    }

    private function updateMigrationTable(string $olderVersion)
    {
        if ($olderVersion != '0.0.0') {
            Artisan::call('migrate', ['--path' => 'database/migrations', '--force' => true]);
        }
    }

    private function handleArtisanLogs()
    {
        $this->log = $this->log."\n\n".Artisan::output();
    }

    private function clearViewCache()
    {
        Artisan::call('view:clear');
        $this->handleArtisanLogs();
    }

    private function clearConfig()
    {
        Artisan::call('config:clear');
        $this->handleArtisanLogs();
    }

    private function isInstall()
    {
        $check = false;
        $env = base_path('.env');
        if (File::exists($env) && env('DB_INSTALL') == 1) {
            $check = true;
        }
        return $check;
    }
    private function setDBInstall($value)
    {
        try {
            $this->writeToEnvAndRunConfigClear('DB_INSTALL', $value);
           
        } catch (Exception $e) {
        
            return errorResponse($e->getMessage());
        }
    }
}
