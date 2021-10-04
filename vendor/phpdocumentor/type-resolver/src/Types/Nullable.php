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
 * Value Object representing a nullable type. The real type is wrapped.
 *
 * @psalm-immutable
 */
final class Nullable implements Type
{
    /** @var Type The actual type that is wrapped */
    private $realType;

    /**
     * Initialises this nullable type using the real type embedded
     */
    public function __construct(Type $realType)
    {
        $this->realType = $realType;
    }

    /**
     * Provide access to the actual type directly, if needed.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getActualType(): Type
=======
    public function getActualType() : Type
>>>>>>> 22c0e54 (table changes)
=======
    public function getActualType(): Type
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->realType;
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
        return '?' . $this->realType->__toString();
    }
}
