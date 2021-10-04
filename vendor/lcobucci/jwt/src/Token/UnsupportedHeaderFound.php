<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Token;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class UnsupportedHeaderFound extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function encryption()
=======
    public static function encryption(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function encryption()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Encryption is not supported yet');
    }
}
