<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

/**
 * Value object representing Integer type
 *
 * @psalm-immutable
 */
<<<<<<< HEAD
<<<<<<< HEAD
class Integer implements Type
=======
final class Integer implements Type
>>>>>>> 22c0e54 (table changes)
=======
class Integer implements Type
>>>>>>> f330c64 (optimization in progress)
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function __toString(): string
=======
    public function __toString() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function __toString(): string
>>>>>>> f330c64 (optimization in progress)
    {
        return 'int';
    }
}
