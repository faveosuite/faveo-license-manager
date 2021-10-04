<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer\Ecdsa;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class ConversionFailed extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function invalidLength()
=======
    public static function invalidLength(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function invalidLength()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Invalid signature length.');
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function incorrectStartSequence()
=======
    public static function incorrectStartSequence(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function incorrectStartSequence()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Invalid data. Should start with a sequence.');
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function integerExpected()
=======
    public static function integerExpected(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function integerExpected()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Invalid data. Should contain an integer.');
    }
}
