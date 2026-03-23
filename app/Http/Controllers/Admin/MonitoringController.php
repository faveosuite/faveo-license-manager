<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lang;

class MonitoringController extends Controller
{
    /**
     * Checks whether Pulse/Horizon can be accessed in current installation path.
     */
    public function checkPulseHorizon(Request $request)
    {
        $type = strtolower((string) $request->get('type', ''));

        if ($type !== 'pulse') {
            return errorResponse('Invalid monitoring type', 400);
        }

        $basePath = trim(parse_url(url('/'), PHP_URL_PATH) ?? '', '/');
        $installedInSubdirectory = !empty($basePath);

        $title = Lang::get('lang.pulse_could_not_load');

        return successResponse('', [
            'type' => 'pulse',
            'allowed' => ! $installedInSubdirectory,
            'reason' => $installedInSubdirectory ? 'Invalid Installation Path' : null,
            'message' => $installedInSubdirectory ? ($title.'This page could not open because pulse does not support folder-based installations. Please move the app to the domain root or use a subdomain and try again.') : null,
        ]);
    }
}
