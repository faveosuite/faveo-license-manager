<?php

namespace App\Exceptions;

use Exception;

/**
 * These exceptions will not be reported(or sent to bugsnag)
 */
class CustomException extends Exception
{
    /**
     * @return void
     */
    public function report()
    {
        // no need to report this exception
    }
}
