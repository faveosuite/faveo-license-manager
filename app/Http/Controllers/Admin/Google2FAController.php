<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AflClients;
use App\Models\UserBackupCode;
use Illuminate\Http\Request;
use Google2FA;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;

class Google2FAController extends Controller
{

    public function enableTwoFactor(Request $request)
    {
        try {
            $secret = Google2FA::generateSecretKey();

            $user = getAuthUser();

            $user->google2fa_secret = $secret;
            $user->save();

            $userNameOrEmail = empty($user->client_email) ? $user->client_fname . " " . $user->client_lname : $user->client_email;

            $google2FAQRImage = Google2FA::getQRCodeInline(
                "license manager",
                $userNameOrEmail,
                $secret,
                150
            );
            return successResponse('', ['image' => $google2FAQRImage, 'secret' => $secret]);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function disableTwoFactor(Request $request)
    {
        $user = getAuthUser();
        $user->google2fa_secret = null;
        $user->google2fa_activation_date = null;
        $user->is_2fa_enabled = 0;
        $user->save();
        empty(UserBackupCode::where('client_id', getAuthUserId())->get()) ? : UserBackupCode::where('client_id', getAuthUserId())->delete();;
        return successResponse(Lang::get('lang.2fa_disabled'));
    }
    public function generateRecoveryCode()
    {
        $items = $this->createCode();
        $this->saveRecoveryCode($items);

        return successResponse('', ['code' => $items]);
    }
    private function saveRecoveryCode($code)
    {
        if (! empty(AflClients::where('client_id', getAuthUserId()))) {
            if (empty(UserBackupCode::where('client_id', getAuthUserId())->get()->toArray())) {
                $this->createStoreRecoveryCode($code);
            }
            if (! empty(UserBackupCode::where('client_id', getAuthUserId())->get()->toArray())) {
                UserBackupCode::where('client_id', getAuthUserId())->delete();
                $this->createStoreRecoveryCode($code);
            }
        }
    }
    private function createStoreRecoveryCode($code)
    {
        for ($i = 0; $i < count($code); $i++) {
            UserBackupCode::create(['client_id' => getAuthUserId(), 'backup_codes' => $code[$i]]);
        }
    }
    private function createCode()
    {
        $i = 0;
        while ($i < 10) {
            $code = str_random(20);
            $codes[] = $code;
            $i++;
        }

        return $codes;
    }
    public function getRecoveryCode()
    {
        try {
            $codes = UserBackupCode::where('client_id', getAuthUserId())->pluck('backup_codes')->toArray();
            if (empty($codes)) {
                $codes = $this->createCode();
                $this->saveRecoveryCode($codes);
            }

            return successResponse('', ['code' => $codes]);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    public function postSetupValidateToken(Request $request)
    {
        try {
            $user = getAuthUser();
            $secret = $user->google2fa_secret;
            $checkValidPasscode = Google2FA::verifyKey($secret, $request->totp);
            if ($checkValidPasscode) {
                $user->is_2fa_enabled = 1;
                $user->google2fa_activation_date = \Carbon\Carbon::now();
                $user->save();

                return successResponse(Lang::get('lang.valid_passcode'));
            }
            return errorResponse(Lang::get('lang.invalid_passcode'),400);
        }
        catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    public function verifyPassword(Request $request)
    {
        try {
            $user = getAuthUser();
            $cacheKey = "User " . $user->client_id . "Password confirm at";
            $password = $request->input('password');

            $passwordVerified = \Hash::check($password, $user->client_password);

            if ($passwordVerified) {
                Cache::put($cacheKey, true, now()->addMinutes(5));

                return successResponse(Lang::get('lang.password_verified'));
            }

            return errorResponse(Lang::get('lang.password_incorrect'),400);

        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
    public function downloadRecoveryCodes()
    {
        $codes = UserBackupCode::where('client_id',getAuthUserId())->pluck('backup_codes')->toArray();
        $sortCodes = implode(',', $this->sortCodes($codes));
        $codes = "SAVE YOUR BACKUP CODES\nKeep these backup codes somewhere safe but accessible.\n\n,".$sortCodes.",\n\n(".$this->checkForEmptyEmail()."),* You can only use each backup code once.\n* Need more? ".url('/');
        $codes = preg_replace('*,*', "\r \n", $codes);
        $fileName = 'Backup-recovery-codes-'.$this->checkForEmptyEmail().'.txt';

        return response()->streamDownload(function () use ($codes) {
            echo $codes;
        }, $fileName);
    }
    private function checkForEmptyEmail()
    {
        $email = AflClients::where('client_id', getAuthUserId())->value('client_email');
        if (empty($email)) {
            return AflClients::where('client_id', getAuthUserId())->value('client_username');
        }

        return $email;
    }

    /**
     * Sorts and arranges code for download
     */
    private function sortCodes($codes)
    {
        $codesCount = count($codes);
        for ($k = 0; $k < $codesCount; $k += 2) {
            $sortCodes[] = $codes[$k].'           '.$code = ($k == $codesCount - 1) ? '' : $codes[$k + 1];
        }
        return $sortCodes;
    }
    public function showVerifyPasswordPopup()
    {
        try {
            return successResponse('', 'password_confirmation_not_required');
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
