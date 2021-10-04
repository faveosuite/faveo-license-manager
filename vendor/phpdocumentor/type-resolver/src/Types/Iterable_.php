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

/**
 * Value Object representing iterable type
 *
 * @psalm-immutable
 */
final class Iterable_ extends AbstractList
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
        if ($this->keyType) {
            return 'iterable<' . $this->keyType . ',' . $this->valueType . '>';
        }

        if ($this->valueType instanceof Mixed_) {
            return 'iterable';
        }

        return 'iterable<' . $this->valueType . '>';
    }
}
