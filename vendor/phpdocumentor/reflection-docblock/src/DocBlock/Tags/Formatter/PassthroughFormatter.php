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

namespace phpDocumentor\Reflection\DocBlock\Tags\Formatter;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use function trim;

class PassthroughFormatter implements Formatter
{
    /**
     * Formats the given tag to return a simple plain text version.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function format(Tag $tag): string
=======
    public function format(Tag $tag) : string
>>>>>>> 22c0e54 (table changes)
=======
    public function format(Tag $tag): string
>>>>>>> f330c64 (optimization in progress)
    {
        return trim('@' . $tag->getName() . ' ' . $tag);
    }
}
