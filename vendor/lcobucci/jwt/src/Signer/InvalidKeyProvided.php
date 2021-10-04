<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class InvalidKeyProvided extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param string $details
     *
     * @return self
     */
    public static function cannotBeParsed($details)
<<<<<<< HEAD
=======
    public static function cannotBeParsed(string $details): self
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('It was not possible to parse your key, reason: ' . $details);
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function incompatibleKey()
=======
    public static function incompatibleKey(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function incompatibleKey()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('This key is not compatible with this signer');
    }
}
