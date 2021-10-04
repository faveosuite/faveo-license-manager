<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use function class_alias;

/**
 * Value Object representing the PseudoType 'False', which is a Boolean type.
 *
 * @psalm-immutable
 */
final class False_ extends Boolean implements PseudoType
{
<<<<<<< HEAD
<<<<<<< HEAD
    public function underlyingType(): Type
=======
    public function underlyingType() : Type
>>>>>>> 22c0e54 (table changes)
=======
    public function underlyingType(): Type
>>>>>>> f330c64 (optimization in progress)
    {
        return new Boolean();
    }

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
        return 'false';
    }
}

class_alias('\phpDocumentor\Reflection\PseudoTypes\False_', 'phpDocumentor\Reflection\Types\False_', false);
