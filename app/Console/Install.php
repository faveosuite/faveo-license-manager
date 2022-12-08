<?php

namespace App\Console\Commands;
use App\Http\Controllers\Dependency\LicenseDependencyController;
use App\Http\Controllers\Installer\InstallerController;
use Illuminate\Console\Command;

class Install extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:license';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'to install license manager';
  
    
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
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
            $this->displayArtLogo();
            if ($this->appEnv()){
                $appUrl = $this->ask('Enter your app url (with only https)');
              
                    if ($this->confirm('Do you want to install license?')) {
                        $default = $this->choice(
                            'Which sql engine would you like to use?', ['mysql']
                        );
                        $formattedAppUrl = $this->formatAppUrl($appUrl);
                        $host = $this->ask('Enter your sql host');
                        $database = $this->ask('Enter your database name');
                        $dbusername = $this->ask('Enter your database username');
                        $dbpassword = $this->ask('Enter your database password (blank if not entered)', false);
                        $port = $this->ask('Enter your sql port (blank if not entered)', false);
                        $this->install->env($default, $host, $port, $database, $dbusername, $dbpassword, $formattedAppUrl);
                        $this->info('.env file has created');
                        $this->alert("please run 'php artisan install:db'");
                    } else {
                        $this->info('We hope, you will try next time');
                    }
                
            } else {
                $this->info('License can not be installed on your server. Please configure your server to meet above requirements and try again.');
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }
    }
    
      /**
     * Removes trailing slash from the url
     * @param  string $url
     * @return string
     */
    public function formatAppUrl(string $url) : string
    {
        if (str_finish($url, '/')) {
            $url = rtrim($url, "/ ");
        }
        return $url;
    }
    
        public function appEnv() {
        $extensions = [
            'curl', 'ctype', 'imap', 'mbstring',
            'openssl', 'tokenizer', 'zip',
            'pdo', 'mysqli', 'bcmath', 'iconv',
            'XML', 'json',  'fileinfo',
        ];
        $result = [];
        $can_install = true;
        foreach ($extensions as $key => $extension) {
            $result[$key]['extension'] = $extension;
            if (!extension_loaded($extension)) {
                $result[$key]['status'] = "Not Loading, Please open please open '" . php_ini_loaded_file() . "' and add 'extension = " . $extension;
                $can_install = false;
            } else {
                $result[$key]['status'] = "Loading";
            }
        }
        $result['php']['extension'] = 'PHP';
        if (phpversion() >= 7.0) {
            $result['php']['status'] = "PHP version supports";
        } else {
            $can_install = false;
            $result['php']['status'] = "PHP version doesn't supports please upgrade to 7.0+";
        }

        $headers = ['Extension', 'Status'];
        $this->table($headers, $result);

        return $can_install;
    }
    
    
       /**
     * Display Faveo's ASCII art logo in CLI
     *
     * @return void
     */
    private function displayArtLogo()
    {
        $this->line("
 ______                      _    _      _           _           _    
|  ____|                    | |  | |    | |         | |         | |   
| |__ __ ___   _____  ___   | |__| | ___| |_ __   __| | ___  ___| | __
|  __/ _` \ \ / / _ \/ _ \  |  __  |/ _ \ | '_ \ / _` |/ _ \/ __| |/ /
| | | (_| |\ V /  __/ (_) | | |  | |  __/ | |_) | (_| |  __/\__ \   < 
|_|  \__,_| \_/ \___|\___/  |_|  |_|\___|_| .__/ \__,_|\___||___/_|\_\
                                          | |                         
                                          |_|                         
");
    }
}
