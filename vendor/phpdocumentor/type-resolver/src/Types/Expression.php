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
 * Represents an expression type as described in the PSR-5, the PHPDoc Standard.
 *
 * @psalm-immutable
 */
final class Expression implements Type
{
    /** @var Type */
    protected $valueType;

    /**
     * Initializes this representation of an array with the given Type.
     */
    public function __construct(Type $valueType)
    {
        $this->valueType = $valueType;
    }

    /**
     * Returns the value for the keys of this array.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getValueType(): Type
=======
    public function getValueType() : Type
>>>>>>> 22c0e54 (table changes)
=======
    public function getValueType(): Type
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->valueType;
    }

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
        return '(' . $this->valueType . ')';
    }
}
