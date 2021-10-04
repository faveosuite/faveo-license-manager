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

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\DocBlock\Tag;
<<<<<<< HEAD
<<<<<<< HEAD
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
=======
>>>>>>> 22c0e54 (table changes)
=======
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
>>>>>>> f330c64 (optimization in progress)
use Webmozart\Assert\Assert;

final class DocBlock
{
    /** @var string The opening line for this docblock. */
    private $summary;

    /** @var DocBlock\Description The actual description for this docblock. */
    private $description;

    /** @var Tag[] An array containing all the tags in this docblock; except inline. */
    private $tags = [];

    /** @var Types\Context|null Information about the context of this DocBlock. */
    private $context;

    /** @var Location|null Information about the location of this DocBlock. */
    private $location;

    /** @var bool Is this DocBlock (the start of) a template? */
    private $isTemplateStart;

    /** @var bool Does this DocBlock signify the end of a DocBlock template? */
    private $isTemplateEnd;

    /**
     * @param DocBlock\Tag[] $tags
     * @param Types\Context  $context  The context in which the DocBlock occurs.
     * @param Location       $location The location within the file that this DocBlock occurs in.
     */
    public function __construct(
        string $summary = '',
        ?DocBlock\Description $description = null,
        array $tags = [],
        ?Types\Context $context = null,
        ?Location $location = null,
        bool $isTemplateStart = false,
        bool $isTemplateEnd = false
    ) {
        Assert::allIsInstanceOf($tags, Tag::class);

        $this->summary     = $summary;
        $this->description = $description ?: new DocBlock\Description('');
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        $this->context  = $context;
        $this->location = $location;

        $this->isTemplateEnd   = $isTemplateEnd;
        $this->isTemplateStart = $isTemplateStart;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function getSummary(): string
=======
    public function getSummary() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function getSummary(): string
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->summary;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function getDescription(): DocBlock\Description
=======
    public function getDescription() : DocBlock\Description
>>>>>>> 22c0e54 (table changes)
=======
    public function getDescription(): DocBlock\Description
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->description;
    }

    /**
     * Returns the current context.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getContext(): ?Types\Context
=======
    public function getContext() : ?Types\Context
>>>>>>> 22c0e54 (table changes)
=======
    public function getContext(): ?Types\Context
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->context;
    }

    /**
     * Returns the current location.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getLocation(): ?Location
=======
    public function getLocation() : ?Location
>>>>>>> 22c0e54 (table changes)
=======
    public function getLocation(): ?Location
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->location;
    }

    /**
     * Returns whether this DocBlock is the start of a Template section.
     *
     * A Docblock may serve as template for a series of subsequent DocBlocks. This is indicated by a special marker
     * (`#@+`) that is appended directly after the opening `/**` of a DocBlock.
     *
     * An example of such an opening is:
     *
     * ```
     * /**#@+
     *  * My DocBlock
     *  * /
     * ```
     *
     * The description and tags (not the summary!) are copied onto all subsequent DocBlocks and also applied to all
     * elements that follow until another DocBlock is found that contains the closing marker (`#@-`).
     *
     * @see self::isTemplateEnd() for the check whether a closing marker was provided.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function isTemplateStart(): bool
=======
    public function isTemplateStart() : bool
>>>>>>> 22c0e54 (table changes)
=======
    public function isTemplateStart(): bool
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->isTemplateStart;
    }

    /**
     * Returns whether this DocBlock is the end of a Template section.
     *
     * @see self::isTemplateStart() for a more complete description of the Docblock Template functionality.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function isTemplateEnd(): bool
=======
    public function isTemplateEnd() : bool
>>>>>>> 22c0e54 (table changes)
=======
    public function isTemplateEnd(): bool
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->isTemplateEnd;
    }

    /**
     * Returns the tags for this DocBlock.
     *
     * @return Tag[]
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getTags(): array
=======
    public function getTags() : array
>>>>>>> 22c0e54 (table changes)
=======
    public function getTags(): array
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->tags;
    }

    /**
     * Returns an array of tags matching the given name. If no tags are found
     * an empty array is returned.
     *
     * @param string $name String to search by.
     *
     * @return Tag[]
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getTagsByName(string $name): array
=======
    public function getTagsByName(string $name) : array
>>>>>>> 22c0e54 (table changes)
=======
    public function getTagsByName(string $name): array
>>>>>>> f330c64 (optimization in progress)
    {
        $result = [];

        foreach ($this->getTags() as $tag) {
            if ($tag->getName() !== $name) {
                continue;
            }

            $result[] = $tag;
        }

        return $result;
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * Returns an array of tags with type matching the given name. If no tags are found
     * an empty array is returned.
     *
     * @param string $name String to search by.
     *
     * @return TagWithType[]
     */
    public function getTagsWithTypeByName(string $name): array
    {
        $result = [];

        foreach ($this->getTagsByName($name) as $tag) {
            if (!$tag instanceof TagWithType) {
                continue;
            }

            $result[] = $tag;
        }

        return $result;
    }

    /**
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     * Checks if a tag of a certain type is present in this DocBlock.
     *
     * @param string $name Tag name to check for.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function hasTag(string $name): bool
=======
    public function hasTag(string $name) : bool
>>>>>>> 22c0e54 (table changes)
=======
    public function hasTag(string $name): bool
>>>>>>> f330c64 (optimization in progress)
    {
        foreach ($this->getTags() as $tag) {
            if ($tag->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove a tag from this DocBlock.
     *
     * @param Tag $tagToRemove The tag to remove.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function removeTag(Tag $tagToRemove): void
=======
    public function removeTag(Tag $tagToRemove) : void
>>>>>>> 22c0e54 (table changes)
=======
    public function removeTag(Tag $tagToRemove): void
>>>>>>> f330c64 (optimization in progress)
    {
        foreach ($this->tags as $key => $tag) {
            if ($tag === $tagToRemove) {
                unset($this->tags[$key]);
                break;
            }
        }
    }

    /**
     * Adds a tag to this DocBlock.
     *
     * @param Tag $tag The tag to add.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function addTag(Tag $tag): void
=======
    private function addTag(Tag $tag) : void
>>>>>>> 22c0e54 (table changes)
=======
    private function addTag(Tag $tag): void
>>>>>>> f330c64 (optimization in progress)
    {
        $this->tags[] = $tag;
    }
}
