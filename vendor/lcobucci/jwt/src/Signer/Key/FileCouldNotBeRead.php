<?php
<<<<<<< HEAD
<<<<<<< HEAD

namespace Lcobucci\JWT\Signer\Key;

use Lcobucci\JWT\Exception;
use InvalidArgumentException;

if (PHP_MAJOR_VERSION === 7) {
    final class FileCouldNotBeRead extends InvalidArgumentException implements Exception
    {
        /** @return self */
        public static function onPath(string $path, \Throwable $cause = null)
        {
            return new self(
                'The path "' . $path . '" does not contain a valid key file',
                0,
                $cause
            );
        }
    }
} else {
    final class FileCouldNotBeRead extends InvalidArgumentException implements Exception
    {
        /**
         * @param string $path
         * @param \Exception|null $cause
         *
         * @return self
         */
        public static function onPath($path, \Exception $cause = null)
        {
            return new self(
                'The path "' . $path . '" does not contain a valid key file',
                0,
                $cause
            );
        }
=======
declare(strict_types=1);
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer\Key;

use Lcobucci\JWT\Exception;
use InvalidArgumentException;

if (PHP_MAJOR_VERSION === 7) {
    final class FileCouldNotBeRead extends InvalidArgumentException implements Exception
    {
        /** @return self */
        public static function onPath(string $path, \Throwable $cause = null)
        {
            return new self(
                'The path "' . $path . '" does not contain a valid key file',
                0,
                $cause
            );
        }
    }
} else {
    final class FileCouldNotBeRead extends InvalidArgumentException implements Exception
    {
<<<<<<< HEAD
        return new self(
            'The path "' . $path . '" does not contain a valid key file',
            0,
            $cause
        );
>>>>>>> 22c0e54 (table changes)
=======
        /**
         * @param string $path
         * @param \Exception|null $cause
         *
         * @return self
         */
        public static function onPath($path, \Exception $cause = null)
        {
            return new self(
                'The path "' . $path . '" does not contain a valid key file',
                0,
                $cause
            );
        }
>>>>>>> f330c64 (optimization in progress)
    }
}
