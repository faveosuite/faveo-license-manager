<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use App\Models\AflSettings;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class PhpMailController extends Controller
{

    
    public function sendEmail($to, $subject, $view, $data)
    {
        try {
            $emailConfig = AflSettings::find(1);
            $emailFromAddress = $emailConfig->EMAIL_FROM_ADDRESS;
            $emailFromName = $emailConfig->EMAIL_FROM_NAME; // Add this line
    
            $this->configSet($emailConfig);

            Mail::send($view, $data, function ($message) use ($to, $subject, $emailFromAddress, $emailFromName) {
                $message->to($to)
                    ->subject($subject)
                    ->from($emailFromAddress, $emailFromName); // Use array for "from" with email and name
            });
    
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function configSet($emailConfig)
    {
        Config::set('mail.host', $emailConfig['EMAIL_HOST']);
        Config::set('mail.port', $emailConfig['EMAIL_PORT']);
        Config::set('mail.password', $emailConfig['EMAIL_PASSWORD']);
        Config::set('mail.security', $emailConfig['EMAIL_ENCRYPTION']);
        Config::set('mail.driver', $emailConfig['EMAIL_DRIVER']);
        Config::set('mail.from_address', $emailConfig['EMAIL_FROM_ADDRESS']);
        Config::set('mail.from.name', $emailConfig['EMAIL_FROM_NAME']);
        Config::set('mail.username', $emailConfig['EMAIL_FROM_ADDRESS']);
    }

}
