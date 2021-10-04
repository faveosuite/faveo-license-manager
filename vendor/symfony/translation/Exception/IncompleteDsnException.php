<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Exception;

class IncompleteDsnException extends InvalidArgumentException
{
<<<<<<< HEAD
<<<<<<< HEAD
    public function __construct(string $message, string $dsn = null, \Throwable $previous = null)
=======
    public function __construct(string $message, string $dsn = null, ?\Throwable $previous = null)
>>>>>>> 22c0e54 (table changes)
=======
    public function __construct(string $message, string $dsn = null, \Throwable $previous = null)
>>>>>>> f330c64 (optimization in progress)
    {
        if ($dsn) {
            $message = sprintf('Invalid "%s" provider DSN: ', $dsn).$message;
        }

        parent::__construct($message, 0, $previous);
    }
}
