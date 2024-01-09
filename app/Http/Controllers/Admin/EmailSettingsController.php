<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EmailSettingRequest;
use App\Models\AflSettings;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\PhpMailController;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class EmailSettingsController extends Controller
{
    protected $emailConfig;
    protected $error;

    protected function checkSConnection(AflSettings $emailConfig)
    {
        try {
            $this->emailConfig = $emailConfig;
        } catch (\Exception $e) {
            $this->error = $e;

            return false;
        }
    }

    public function postSettingsEmail(EmailSettingRequest $request)
    {
        try {
            $emailSettings = $request->all();
            $this->emailConfig = AflSettings::first();
            $this->emailConfig->fill($emailSettings);
        
            if (!$this->checkSendConnection($this->emailConfig)) {
                return errorResponse($this->errorhandler());
            }
            $this->emailConfig->EMAIL_SENDING_STATUS = 1;
            $this->emailConfig->save();

            return successResponse(Lang::get('lang.email_sent'));
        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    private function errorhandler()
    {
        $message = method_exists($this->error, 'getMessage') ? $this->error->getMessage() : $this->error;

        return $message;
    }

    protected function checkSendConnection(AflSettings $emailConfig)
    {
        try {
            $this->emailConfig = $emailConfig;
            // If sending protocol is 'mail', no connection check is required
            $this->setServiceConfig($this->emailConfig);
            if ($this->emailConfig->EMAIL_DRIVER == 'mail') {
                return $this->checkMailConnection();
            }
            // Set outgoing mail configuration to the passed one

            if ($this->emailConfig->EMAIL_DRIVER == 'smtp') {
                return $this->checkSMTPConnection();
            }

            return $this->checkServices();
        } catch (\Exception $e) {
            $this->error = $e;

            return false;
        }
    }

    private function checkMailConnection()
    {
        if (function_exists('mail')) {
            return true;
        }
        $this->error = Lang::get('lang.php_disabled');

        return false;
    }

    private function checkServices()
    {

        try {
            $protocolName = $this->emailConfig->sending_protocol;

            //sending a text message and checking if respond comes. If yes, connection is considered to be successful
            Mail::raw("Testing $protocolName connection", function ($message) {
                $message->to($this->emailConfig->email_address);
            });
            return true;
        } catch(\Exception $e) {
            $this->error = $e;

            return false;
        }
    }

    private function checkSMTPConnection()
    {
        try {
            $transport = new EsmtpTransport(Config::get('mail.host'), Config::get('mail.port'));
            $transport->setUsername(\Config::get('mail.username'));
            $transport->setPassword(\Config::get('mail.password'));
    
            $transport->start();
    
            return true;
        } catch(\Throwable $e) {
            $this->error = $e;
    
            return false;
        } catch(\Exception $e) {
            $this->error = $e;
    
            return false;
        }
    }
   public function setEnv($key, $value)
    {
        $envPath = base_path('.env');

        // Read the current .env file
        $currentEnv = File::get($envPath);

        // Replace the existing value or add a new key-value pair
        $newEnv = preg_replace("/{$key}=.*/", "{$key}={$value}", $currentEnv);

        // Update the .env file with the new content
        File::put($envPath, $newEnv);
    }
    //setting mail driver as $sending protocol
   public function setServiceConfig($emailConfig)
    {
        $sendingProtocol = $emailConfig->EMAIL_DRIVER;
        if ($sendingProtocol && $sendingProtocol != 'smtp' && $sendingProtocol != 'mail') {
            $services = Config::get("services.$sendingProtocol");
            $dynamicServiceConfig = [];

            //loop over it and assign according to the keys given by user
            foreach ($services as $key => $value) {
                $dynamicServiceConfig[$key] = isset($emailConfig[$key]) ? $emailConfig[$key] : $value;
            }

            //setting that service configuration
            Config::set("services.$sendingProtocol", $dynamicServiceConfig);
        } else {
            try{
                $mailController = new PhpMailController();

                $mailController->configSet($emailConfig);
        }
        catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }}
    //setting the config again in the service container
    (new \Illuminate\Mail\MailServiceProvider(app()))->register();
}


}
