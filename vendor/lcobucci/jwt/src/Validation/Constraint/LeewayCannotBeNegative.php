<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Validation\Constraint;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class LeewayCannotBeNegative extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function create()
=======
    public static function create(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function create()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Leeway cannot be negative');
    }
}
