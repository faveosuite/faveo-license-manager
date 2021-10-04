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

use function sprintf;

final class RegisteredClaimGiven extends InvalidArgumentException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
    const DEFAULT_MESSAGE = 'Builder#withClaim() is meant to be used for non-registered claims, '
                                  . 'check the documentation on how to set claim "%s"';

    /**
     * @param string $name
     *
     * @return self
     */
    public static function forClaim($name)
=======
    private const DEFAULT_MESSAGE = 'Builder#withClaim() is meant to be used for non-registered claims, '
                                  . 'check the documentation on how to set claim "%s"';

    public static function forClaim(string $name): self
>>>>>>> 22c0e54 (table changes)
=======
    const DEFAULT_MESSAGE = 'Builder#withClaim() is meant to be used for non-registered claims, '
                                  . 'check the documentation on how to set claim "%s"';

    /**
     * @param string $name
     *
     * @return self
     */
    public static function forClaim($name)
>>>>>>> f330c64 (optimization in progress)
    {
        return new self(sprintf(self::DEFAULT_MESSAGE, $name));
    }
}
