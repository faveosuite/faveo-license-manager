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

namespace phpDocumentor\Reflection\DocBlock\Tags;

use phpDocumentor\Reflection\DocBlock\Tag;

interface Formatter
{
    /**
     * Formats a tag into a string representation according to a specific format, such as Markdown.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function format(Tag $tag): string;
=======
    public function format(Tag $tag) : string;
>>>>>>> 22c0e54 (table changes)
=======
    public function format(Tag $tag): string;
>>>>>>> f330c64 (optimization in progress)
}
