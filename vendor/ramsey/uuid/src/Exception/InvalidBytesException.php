<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Exception;

use RuntimeException as PhpRuntimeException;

/**
 * Thrown to indicate that the bytes being operated on are invalid in some way
 */
<<<<<<< HEAD
<<<<<<< HEAD
class InvalidBytesException extends PhpRuntimeException implements UuidExceptionInterface
=======
class InvalidBytesException extends PhpRuntimeException
>>>>>>> 22c0e54 (table changes)
=======
class InvalidBytesException extends PhpRuntimeException implements UuidExceptionInterface
>>>>>>> f330c64 (optimization in progress)
{
}
