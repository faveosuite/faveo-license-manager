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

final class InvalidTokenStructure extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function missingOrNotEnoughSeparators()
=======
    public static function missingOrNotEnoughSeparators(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function missingOrNotEnoughSeparators()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('The JWT string must have two dots');
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param string $part
     *
     * @return self
     */
    public static function arrayExpected($part)
<<<<<<< HEAD
=======
    public static function arrayExpected(string $part): self
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return new self($part . ' must be an array');
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param string $value
     *
     * @return self
     */
    public static function dateIsNotParseable($value)
<<<<<<< HEAD
=======
    public static function dateIsNotParseable(string $value): self
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Value is not in the allowed date format: ' . $value);
    }
}
