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

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;

/**
 * @deprecated Transition to {@see Rfc4122UuidBuilder}.
 *
 * @psalm-immutable
 */
<<<<<<< HEAD
<<<<<<< HEAD
class DefaultUuidBuilder extends Rfc4122UuidBuilder
=======
class DefaultUuidBuilder extends Rfc4122UuidBuilder implements UuidBuilderInterface
>>>>>>> 22c0e54 (table changes)
=======
class DefaultUuidBuilder extends Rfc4122UuidBuilder
>>>>>>> f330c64 (optimization in progress)
{
}
