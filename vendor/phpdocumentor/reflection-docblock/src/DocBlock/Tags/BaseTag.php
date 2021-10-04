<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\DocBlock\Tags;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Description;

/**
 * Parses a tag definition for a DocBlock.
 */
abstract class BaseTag implements DocBlock\Tag
{
    /** @var string Name of the tag */
    protected $name = '';

    /** @var Description|null Description of the tag. */
    protected $description;

    /**
     * Gets the name of this tag.
     *
     * @return string The name of this tag.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getName(): string
=======
    public function getName() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function getName(): string
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->name;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function getDescription(): ?Description
=======
    public function getDescription() : ?Description
>>>>>>> 22c0e54 (table changes)
=======
    public function getDescription(): ?Description
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->description;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function render(?Formatter $formatter = null): string
=======
    public function render(?Formatter $formatter = null) : string
>>>>>>> 22c0e54 (table changes)
=======
    public function render(?Formatter $formatter = null): string
>>>>>>> f330c64 (optimization in progress)
    {
        if ($formatter === null) {
            $formatter = new Formatter\PassthroughFormatter();
        }

        return $formatter->format($this);
    }
}
