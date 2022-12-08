<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cache;
use Session;
use Redirect;
use View;
use Input;
use Artisan;
use App\Http\Controllers\SyncLicenseToLatestVersion;
use DB;
use App\Models\AflAdmins;
class InstallerController extends Controller
{
    

     
    /**
     * Get Configuration (step 4)
     * checking prerequisites.
     *
     * @return type view
     */
    public function configuration(Request $request)
    {
        return View('installer/view1');
      
    }
    
    
    /**
     * Post configurationcheck
     * checking prerequisites.
     *
     * @return type view
     */
    public function configurationcheck(Request $request)
    {
        Cache::forever('config-check', 'config-check');
        Session::put('default', 'mysql');
        Session::put('host', $request->input('host'));
        Session::put('databasename', $request->input('databasename'));
        Session::put('username', $request->input('username'));
        Session::put('password', $request->input('password'));
        Session::put('port', $request->input('port'));
        Session::put('db_ssl_key', $request->input('db_ssl_key')?:null);
        Session::put('db_ssl_cert', $request->input('db_ssl_cert')?:null);
        Session::put('db_ssl_ca', $request->input('db_ssl_ca')?:null);
        Session::put('db_ssl_verify', $request->input('db_ssl_verify')?:null);
        Cache::forever('dummy_data_installation', false);
        return View::make('installer/view2');
        // return Redirect::route('database');
    }
    
     /**
     * Get database
     * checking prerequisites.
     *
     * @return type view
     */
    public function database(Request $request)
    {
    // checking if the installation is running for the first time or not
        if (Cache::get('config-check') == 'config-check'){
            return View::make('themes/default1/installer/helpdesk/view4');
        } else{
            return Redirect::route('config');
        }
    }
        /**
     * Get account
     * checking prerequisites.
     *
     * @return type view
     */
    public function account(Request $request)
    {
       
             return View::make('installer/demo');
          
        
    }

     public function checkPreInstall()
    {
        Artisan::call('key:generate', ['--force' => true]);

        $url = url('migrate');
        $result = ['success' => 'Pre migration has been tested successfully', 'next' => 'Migrating tables in database', 'api' => $url];
        return response()->json(compact('result'));
    }
    
        public function migrate()
    {
        $db_install_method = '';
        try {
            if(Cache::get('databasename') != env('DB_DATABASE')) {
                throw new Exception("Database connection did not update.", 500);
            }
            $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
            //allowing migrations table in db as it does not get removed on "migrate:reset"
            $tableNames = array_unique(array_merge(['migrations'], $tableNames ));
            if (count($tableNames) === 1) {
                (new SyncLicenseToLatestVersion)->sync();

                if (Cache::get('dummy_data_installation')) {
                    $path = base_path().DIRECTORY_SEPARATOR.'DB'.DIRECTORY_SEPARATOR.'dummy-data.sql';
                    \DB::unprepared(file_get_contents($path));
                }
            }

        } catch (Exception $ex) {
            $this->rollBackMigration();
            $result = ['error' => $ex->getMessage()];
            return response()->json(compact('result'), 500);
        }

        $message = 'Database has been setup successfully.';
        $result = ['success' => $message, 'next' => 'Seeding pre configurations data'];
        return response()->json(compact('result'));
    }
    
        public function createEnv($api = true)
    {
     
        try {
            if (request()->get('default')) {
                $default = request()->get('default');
            } else {
                $default = Session::get('default');
            }
            if (request()->get('host')) {
                $host = request()->get('host');
            } else {
                $host = Session::get('host');
            }
            if (request()->get('databasename')) {
                $database = request()->get('databasename');
            } else {
                $database = Session::get('databasename');
            }
            if (request()->get('username')) {
                $dbusername = request()->get('username');
            } else {
                $dbusername = Session::get('username');
            }
            if (request()->get('password')) {
                $dbpassword = request()->get('password');
            } else {
                $dbpassword = Session::get('password');
            }
            if (request()->get('port')) {
                $port = request()->get('port');
            } else {
                $port = Session::get('port');
            }
            if (request()->get('db_ssl_key')) {
                $sslKey = request()->get('db_ssl_key');
            } else {
                $sslKey = Session::get('db_ssl_key');
            }
            if (request()->get('db_ssl_cert')) {
                $sslCert = request()->get('db_ssl_cert');
            } else {
                $sslCert = Session::get('db_ssl_cert');
            }
            if (request()->get('db_ssl_ca')) {
                $sslCa = request()->get('db_ssl_ca');
            } else {
                $sslCa = Session::get('db_ssl_ca');
            }
            if (request()->get('verify_ssl_peer')) {
                $sslVerify = request()->get('db_ssl_verify');
            } else {
                $sslVerify = Session::get('db_ssl_verify');
            }
            $this->env($default, $host, $port, $database, $dbusername, $dbpassword, null, $sslKey, $sslCert, $sslCa, $sslVerify);
        } catch (Exception $ex) {
        
            $result = [$ex->getMessage()];

            return response()->json(compact('result'), 500);
        }
        if ($api) {
            Cache::forever('databasename', $database);
            $url = url('preinstall/check');
            $result = ['success' => 'Environment configuration file has been created successfully', 'next' => 'Running pre migration test', 'api' => $url];
            return response()->json(compact('result'));
        }
    }
 
    public function env($default, $host, $port, $database, $dbusername, $dbpassword, $appUrl = null)
    {
        $ENV['APP_NAME'] = 'Faveo:'.md5(uniqid());
        $ENV['APP_DEBUG'] = 'false';
        $ENV['APP_BUGSNAG'] = 'true';
        $ENV['APP_URL'] = $appUrl ? $appUrl : url('/'); // for CLI installation
        $ENV['APP_KEY'] = "base64:h3KjrHeVxyE+j6c8whTAs2YI+7goylGZ/e2vElgXT6I=";
        $ENV['DB_TYPE'] = $default;
        $ENV['DB_HOST'] = '"'.$host.'"';
        $ENV['DB_PORT'] = '"'.$port.'"';
        $ENV['DB_DATABASE'] = '"'.$database.'"';
        $ENV['DB_USERNAME'] = '"'.$dbusername.'"';
        $ENV['DB_PASSWORD'] = '"'.str_replace('"', '\"', $dbpassword).'"';
        $ENV['DB_ENGINE']   = 'InnoDB'; //must be removed after fixing code for foreign key contraints for InnoDB
        $ENV['MAIL_DRIVER'] = 'smtp';
        $ENV['MAIL_HOST'] = 'mailtrap.io';
        $ENV['MAIL_PORT'] = '2525';
        $ENV['MAIL_USERNAME'] = 'null';
        $ENV['MAIL_PASSWORD'] = 'null';
        $ENV['CACHE_DRIVER'] = 'file';
        $ENV['SESSION_DRIVER'] = 'file';
        $ENV['SESSION_COOKIE_NAME'] = 'faveo_'.  rand(0, 10000);
        $ENV['QUEUE_DRIVER'] = 'sync';
        $ENV['FCM_SERVER_KEY'] = 'AIzaSyBJNRvyub-_-DnOAiIJfuNOYMnffO2sfw4';
        $ENV['FCM_SENDER_ID'] = '505298756081';
        $ENV['PROBE_PASS_PHRASE'] = md5(uniqid());
        $ENV['REDIS_DATABASE'] = '0';
        $ENV['BROADCAST_DRIVER']='pusher';
        $ENV['LARAVEL_WEBSOCKETS_ENABLED']='false';
        $ENV['LARAVEL_WEBSOCKETS_PORT']=6001;
        $ENV['LARAVEL_WEBSOCKETS_HOST'] ='127.0.0.1';
        $ENV['LARAVEL_WEBSOCKETS_SCHEME'] ='http';
        $ENV['PUSHER_APP_ID'] = str_random(16);
        $ENV['PUSHER_APP_KEY'] = md5(uniqid());
        $ENV['PUSHER_APP_SECRET'] = md5(uniqid());
        $ENV['PUSHER_APP_CLUSTER'] = 'mt1';
        $ENV['MIX_PUSHER_APP_KEY']='"${PUSHER_APP_KEY}"';
        $ENV['MIX_PUSHER_APP_CLUSTER']='"${PUSHER_APP_CLUSTER}"';
        $ENV['SOCKET_CLIENT_SSL_ENFORCEMENT'] = 'false';
        $ENV['LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT'] = 'null';
        $ENV['LARAVEL_WEBSOCKETS_SSL_LOCAL_PK'] = 'null';
        $ENV['LARAVEL_WEBSOCKETS_SSL_PASSPHRASE'] = 'null';
        $config = '';
        foreach ($ENV as $key => $val) {
            $config .= "{$key}={$val}\n";
        }
        if (is_file(base_path() . DIRECTORY_SEPARATOR . '.env')) {
            unlink(base_path() . DIRECTORY_SEPARATOR . '.env');
        }
        if (!is_file(base_path() . DIRECTORY_SEPARATOR . 'example.env')) {
            fopen(base_path() . DIRECTORY_SEPARATOR . 'example.env', "w");
        }

        // Write environment file
        $fp = fopen(base_path() . DIRECTORY_SEPARATOR . 'example.env', 'w');
        fwrite($fp, $config);
        fclose($fp);
        rename(base_path() . DIRECTORY_SEPARATOR . 'example.env', base_path() . DIRECTORY_SEPARATOR . '.env');
    }



    public function updateInstalEnv(string $environment, $awsCredentials = [])
    {
        $env = base_path() . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($env)) {
            throw new Exception('.env not found');            
        }
        $txt = "DB_INSTALL=1";
        $txt1 = "APP_ENV=$environment";
        file_put_contents($env, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents($env, $txt1 . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        foreach ($awsCredentials as $key => $value) {
            $line = strtoupper($key) . '=' . $value . PHP_EOL;
            file_put_contents($env, $line, FILE_APPEND | LOCK_EX);
        }

        if(Session::get('cache_driver') == "redis"){
            file_put_contents($env, str_replace("CACHE_DRIVER=" . getenv('CACHE_DRIVER'), "CACHE_DRIVER=" . 'redis', file_get_contents($env)));
            QueueService::where('status', 1)->update(['status' => 0]);
            $queue = QueueService::where('short_name', 'redis')->first();
            $queue->status = 1;
            $queue->save();
            $queue->extraFieldRelation()->updateOrCreate(['key' => 'driver'], ['key' => 'driver', 'value' => 'redis']);
            $queue->extraFieldRelation()->updateOrCreate(['key' => 'queue'], ['key' => 'queue', 'value' => 'default']);
        }
       
    }
        /**
     * Post accountcheck
     * checking prerequisites.
     *
     * @param type InstallerRequest $request
     *
     * @return type view
     */
    public function accountcheck(Request $request)
    {
        $validator = \Validator::make($request->all(), [
                    'admin_fname' => 'required|max:20',
                    'admin_lname' => 'required|max:20',
                    'admin_email' => 'required|max:50|email',
                 'admin_password' => ['required','regex:/^(?=\S*[a-z])(?=\S*[A-Z])(?=\S*\d)(?=\S*[^\w\s])\S{8,}/'],
                    
                   
                ],[
                     'admin_password.regex' => 'Password must have 8 characters and contain at least one Uppercase, one lowercase, one number and one special character',
              
                ]);
      
        if ($validator->fails()) {
            return redirect('getting-started?timezone='.$request->input('timezone'))
                            ->withErrors($validator)
                            ->withInput();
        }

        
        // Set variables fetched from input request
        $firstname = $request->input('admin_fname');
        $lastname = $request->input('admin_lname');
        $email = $request->input('admin_email');
        Session::put('admin_email', $email);
        Session::put('cache_driver', $request->cache_driver);

        $password = $request->input('admin_password');

        $language = $request->input('language');
        $timezone = $request->input('timezone');
        $date = $request->input('date');
        $datetime = 'F j, Y, g:i a';

        // creating an user
        // dd($request->all());
        $user = new AflAdmins(array(
        
                    'admin_fname' => $firstname,
                    'admin_lname' => $lastname,
                    'admin_email' => $email,
                    'admin_password' => \Hash::make($password),
                 
        ));
         $user->save();

            Cache::forever('getting-started', 'getting-started');

          return view('installer/final');

        }


}
