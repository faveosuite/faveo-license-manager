<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/apl_callbacks/connection_test.php',
        '/apl_callbacks/license_install.php',
        '/apl_callbacks/license_scheme.php',
        '/apl_callbacks/license_verify.php',
        'pre-license',
    ];
}
