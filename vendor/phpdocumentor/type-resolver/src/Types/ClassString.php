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

use phpDocumentor\Reflection\Fqsen;
<<<<<<< HEAD
<<<<<<< HEAD
use phpDocumentor\Reflection\PseudoType;
=======
>>>>>>> 22c0e54 (table changes)
=======
use phpDocumentor\Reflection\PseudoType;
>>>>>>> f330c64 (optimization in progress)
use phpDocumentor\Reflection\Type;

/**
 * Value Object representing the type 'string'.
 *
 * @psalm-immutable
 */
<<<<<<< HEAD
<<<<<<< HEAD
final class ClassString extends String_ implements PseudoType
=======
final class ClassString implements Type
>>>>>>> 22c0e54 (table changes)
=======
final class ClassString extends String_ implements PseudoType
>>>>>>> f330c64 (optimization in progress)
{
    /** @var Fqsen|null */
    private $fqsen;

    /**
     * Initializes this representation of a class string with the given Fqsen.
     */
    public function __construct(?Fqsen $fqsen = null)
    {
        $this->fqsen = $fqsen;
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    public function underlyingType(): Type
    {
        return new String_();
    }

<<<<<<< HEAD
    /**
     * Returns the FQSEN associated with this object.
     */
    public function getFqsen(): ?Fqsen
=======
    /**
     * Returns the FQSEN associated with this object.
     */
    public function getFqsen() : ?Fqsen
>>>>>>> 22c0e54 (table changes)
=======
    /**
     * Returns the FQSEN associated with this object.
     */
    public function getFqsen(): ?Fqsen
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->fqsen;
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
        if ($this->fqsen === null) {
            return 'class-string';
        }

        return 'class-string<' . (string) $this->fqsen . '>';
    }
}
