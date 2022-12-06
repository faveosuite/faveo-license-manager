<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailsRequest;
use App\Models\AflEmails;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the Custom Emails page in Auto Faveo licenser
 * Class EmailsController
 */
class EmailsController extends Controller
{
    /**
     * To Add or Update custom Email fields in license manager
     *
     * @param  EmailsRequest  $request
     * @param $email_id
     * @return success response wheather the fields has been added or updated
     */
    public function emails(EmailsRequest $request)
    {
        $user = AflEmails::first();
        if (empty($user)) {
            $email = new AflEmails([

                'email_expiring_license_subject' => $request->get('email_expiring_license_subject'),
                'email_expiring_license_text' => $request->get('email_expiring_license_text'),
                'email_expiring_updates_subject' => $request->get('email_expiring_updates_subject'),
                'email_expiring_updates_text' => $request->get('email_expiring_updates_text'),
                'email_expiring_support_subject' => $request->get('email_expiring_support_subject'),
                'email_expiring_support_text' => $request->get('email_expiring_support_text'),
            ]
         );
            $email->save();

            return successResponse(Lang::get('lang.emails'), $email, 201);
        } else {
            $id = AflEmails::first()->value('email_id');

            $email = AflEmails::where('email_id', $id)->update([
                'email_expiring_license_subject' => $request->get('email_expiring_license_subject'),
                'email_expiring_license_text' => $request->get('email_expiring_license_text'),
                'email_expiring_updates_subject' => $request->get('email_expiring_updates_subject'),
                'email_expiring_updates_text' => $request->get('email_expiring_updates_text'),
                'email_expiring_support_subject' => $request->get('email_expiring_support_subject'),
                'email_expiring_support_text' => $request->get('email_expiring_support_text'),
            ]);

            return successResponse(Lang::get('lang.emails'), $email, 200);
        }
    }

    /**
     * Shows the list of all the email fields.
     */
    public function show()
    {
        $emails = AflEmails::all();

        return successResponse(Lang::get('lang.Emails_Show'), $emails, 200);
    }
}
