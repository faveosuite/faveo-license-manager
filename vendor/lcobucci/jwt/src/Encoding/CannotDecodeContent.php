<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Encoding;

use JsonException;
use Lcobucci\JWT\Exception;
use RuntimeException;

final class CannotDecodeContent extends RuntimeException implements Exception
{
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param JsonException $previous
     *
     * @return self
     */
    public static function jsonIssues(JsonException $previous)
<<<<<<< HEAD
=======
    public static function jsonIssues(JsonException $previous): self
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Error while decoding from JSON', 0, $previous);
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function invalidBase64String()
=======
    public static function invalidBase64String(): self
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function invalidBase64String()
>>>>>>> f330c64 (optimization in progress)
    {
        return new self('Error while decoding from Base64Url, invalid base64 characters detected');
    }
}
