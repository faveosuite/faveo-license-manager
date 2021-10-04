<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Token;

/**
 * Defines the list of claims that are registered in the IANA "JSON Web Token Claims" registry
 *
 * @see https://tools.ietf.org/html/rfc7519#section-4.1
 */
interface RegisteredClaims
{
<<<<<<< HEAD
<<<<<<< HEAD
    const ALL = [
=======
    public const ALL = [
>>>>>>> 22c0e54 (table changes)
=======
    const ALL = [
>>>>>>> f330c64 (optimization in progress)
        self::AUDIENCE,
        self::EXPIRATION_TIME,
        self::ID,
        self::ISSUED_AT,
        self::ISSUER,
        self::NOT_BEFORE,
        self::SUBJECT,
    ];

<<<<<<< HEAD
<<<<<<< HEAD
    const DATE_CLAIMS = [
=======
    public const DATE_CLAIMS = [
>>>>>>> 22c0e54 (table changes)
=======
    const DATE_CLAIMS = [
>>>>>>> f330c64 (optimization in progress)
        self::ISSUED_AT,
        self::NOT_BEFORE,
        self::EXPIRATION_TIME,
    ];

    /**
     * Identifies the recipients that the JWT is intended for
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.3
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const AUDIENCE = 'aud';
=======
    public const AUDIENCE = 'aud';
>>>>>>> 22c0e54 (table changes)
=======
    const AUDIENCE = 'aud';
>>>>>>> f330c64 (optimization in progress)

    /**
     * Identifies the expiration time on or after which the JWT MUST NOT be accepted for processing
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.4
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const EXPIRATION_TIME = 'exp';
=======
    public const EXPIRATION_TIME = 'exp';
>>>>>>> 22c0e54 (table changes)
=======
    const EXPIRATION_TIME = 'exp';
>>>>>>> f330c64 (optimization in progress)

    /**
     * Provides a unique identifier for the JWT
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.7
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const ID = 'jti';
=======
    public const ID = 'jti';
>>>>>>> 22c0e54 (table changes)
=======
    const ID = 'jti';
>>>>>>> f330c64 (optimization in progress)

    /**
     * Identifies the time at which the JWT was issued
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.6
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const ISSUED_AT = 'iat';
=======
    public const ISSUED_AT = 'iat';
>>>>>>> 22c0e54 (table changes)
=======
    const ISSUED_AT = 'iat';
>>>>>>> f330c64 (optimization in progress)

    /**
     * Identifies the principal that issued the JWT
     *
     * @see https://tools.ietf.org/html/rfc7519#section-4.1.1
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const ISSUER = 'iss';
=======
    public const ISSUER = 'iss';
>>>>>>> 22c0e54 (table changes)
=======
    const ISSUER = 'iss';
>>>>>>> f330c64 (optimization in progress)

    /**
     * Identifies the time before which the JWT MUST NOT be accepted for processing
     *
     * https://tools.ietf.org/html/rfc7519#section-4.1.5
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const NOT_BEFORE = 'nbf';
=======
    public const NOT_BEFORE = 'nbf';
>>>>>>> 22c0e54 (table changes)
=======
    const NOT_BEFORE = 'nbf';
>>>>>>> f330c64 (optimization in progress)

    /**
     * Identifies the principal that is the subject of the JWT.
     *
     * https://tools.ietf.org/html/rfc7519#section-4.1.2
     */
<<<<<<< HEAD
<<<<<<< HEAD
    const SUBJECT = 'sub';
=======
    public const SUBJECT = 'sub';
>>>>>>> 22c0e54 (table changes)
=======
    const SUBJECT = 'sub';
>>>>>>> f330c64 (optimization in progress)
}
