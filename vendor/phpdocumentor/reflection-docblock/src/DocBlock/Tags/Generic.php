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

use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use function preg_match;

/**
 * Parses a tag definition for a DocBlock.
 */
final class Generic extends BaseTag implements Factory\StaticMethod
{
    /**
     * Parses a tag and populates the member variables.
     *
     * @param string      $name        Name of the tag.
     * @param Description $description The contents of the given tag.
     */
    public function __construct(string $name, ?Description $description = null)
    {
        $this->validateTagName($name);

        $this->name        = $name;
        $this->description = $description;
    }

    /**
     * Creates a new tag that represents any unknown tag type.
     *
     * @return static
     */
    public static function create(
        string $body,
        string $name = '',
        ?DescriptionFactory $descriptionFactory = null,
        ?TypeContext $context = null
<<<<<<< HEAD
<<<<<<< HEAD
    ): self {
=======
    ) : self {
>>>>>>> 22c0e54 (table changes)
=======
    ): self {
>>>>>>> f330c64 (optimization in progress)
        Assert::stringNotEmpty($name);
        Assert::notNull($descriptionFactory);

        $description = $body !== '' ? $descriptionFactory->create($body, $context) : null;

        return new static($name, $description);
    }

    /**
     * Returns the tag as a serialized string
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
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }

        return $description;
    }

    /**
     * Validates if the tag name matches the expected format, otherwise throws an exception.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function validateTagName(string $name): void
=======
    private function validateTagName(string $name) : void
>>>>>>> 22c0e54 (table changes)
=======
    private function validateTagName(string $name): void
>>>>>>> f330c64 (optimization in progress)
    {
        if (!preg_match('/^' . StandardTagFactory::REGEX_TAGNAME . '$/u', $name)) {
            throw new InvalidArgumentException(
                'The tag name "' . $name . '" is not wellformed. Tags may only consist of letters, underscores, '
                . 'hyphens and backslashes.'
            );
        }
    }
}
