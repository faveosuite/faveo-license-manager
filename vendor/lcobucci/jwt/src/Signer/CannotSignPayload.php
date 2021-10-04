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

final class CannotSignPayload extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @pararm string $error
     *
     * @return self
     */
    public static function errorHappened($error)
<<<<<<< HEAD
=======
    public static function errorHappened(string $error): self
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('There was an error while creating the signature: ' . $error);
    }
}
