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

<<<<<<< HEAD
<<<<<<< HEAD
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;

=======
>>>>>>> 22c0e54 (table changes)
=======
use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;

>>>>>>> f330c64 (optimization in progress)
/**
 * Value Object representing a array-key Type.
 *
 * A array-key Type is the supertype (but not a union) of int and string.
 *
 * @psalm-immutable
 */
<<<<<<< HEAD
<<<<<<< HEAD
final class ArrayKey extends AggregatedType implements PseudoType
=======
final class ArrayKey extends AggregatedType
>>>>>>> 22c0e54 (table changes)
=======
final class ArrayKey extends AggregatedType implements PseudoType
>>>>>>> f330c64 (optimization in progress)
{
    public function __construct()
    {
        parent::__construct([new String_(), new Integer()], '|');
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    public function underlyingType(): Type
    {
        return new Compound([new String_(), new Integer()]);
    }

<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    public function __toString(): string
    {
        return 'array-key';
    }
}
