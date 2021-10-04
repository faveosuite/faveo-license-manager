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

namespace phpDocumentor\Reflection\DocBlock;

use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

interface Tag
{
<<<<<<< HEAD
<<<<<<< HEAD
    public function getName(): string;

    /**
     * @return Tag|mixed Class that implements Tag
=======
    public function getName() : string;

    /**
     * @return Tag|mixed Class that implements Tag
     *
>>>>>>> 22c0e54 (table changes)
=======
    public function getName(): string;

    /**
     * @return Tag|mixed Class that implements Tag
>>>>>>> f330c64 (optimization in progress)
     * @phpstan-return ?Tag
     */
    public static function create(string $body);

<<<<<<< HEAD
<<<<<<< HEAD
    public function render(?Formatter $formatter = null): string;

    public function __toString(): string;
=======
    public function render(?Formatter $formatter = null) : string;

    public function __toString() : string;
>>>>>>> 22c0e54 (table changes)
=======
    public function render(?Formatter $formatter = null): string;

    public function __toString(): string;
>>>>>>> f330c64 (optimization in progress)
}
