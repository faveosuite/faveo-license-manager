<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Exception;

use InvalidArgumentException;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use const PREG_BACKTRACK_LIMIT_ERROR;
use const PREG_BAD_UTF8_ERROR;
use const PREG_BAD_UTF8_OFFSET_ERROR;
use const PREG_INTERNAL_ERROR;
use const PREG_JIT_STACKLIMIT_ERROR;
use const PREG_NO_ERROR;
use const PREG_RECURSION_LIMIT_ERROR;

final class PcreException extends InvalidArgumentException
{
<<<<<<< HEAD
<<<<<<< HEAD
    public static function createFromPhpError(int $errorCode): self
=======
    public static function createFromPhpError(int $errorCode) : self
>>>>>>> 22c0e54 (table changes)
=======
    public static function createFromPhpError(int $errorCode): self
>>>>>>> f330c64 (optimization in progress)
    {
        switch ($errorCode) {
            case PREG_BACKTRACK_LIMIT_ERROR:
                return new self('Backtrack limit error');
<<<<<<< HEAD
<<<<<<< HEAD

            case PREG_RECURSION_LIMIT_ERROR:
                return new self('Recursion limit error');

            case PREG_BAD_UTF8_ERROR:
                return new self('Bad UTF8 error');

            case PREG_BAD_UTF8_OFFSET_ERROR:
                return new self('Bad UTF8 offset error');

            case PREG_JIT_STACKLIMIT_ERROR:
                return new self('Jit stacklimit error');

=======
=======

>>>>>>> f330c64 (optimization in progress)
            case PREG_RECURSION_LIMIT_ERROR:
                return new self('Recursion limit error');

            case PREG_BAD_UTF8_ERROR:
                return new self('Bad UTF8 error');

            case PREG_BAD_UTF8_OFFSET_ERROR:
                return new self('Bad UTF8 offset error');

            case PREG_JIT_STACKLIMIT_ERROR:
                return new self('Jit stacklimit error');
<<<<<<< HEAD
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
            case PREG_NO_ERROR:
            case PREG_INTERNAL_ERROR:
            default:
        }

        return new self('Unknown Pcre error');
    }
}
