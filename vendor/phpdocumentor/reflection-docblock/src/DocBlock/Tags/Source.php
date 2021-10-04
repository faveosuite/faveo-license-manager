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

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
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
 * Reflection class for a {@}source tag in a Docblock.
 */
final class Source extends BaseTag implements Factory\StaticMethod
{
    /** @var string */
    protected $name = 'source';

    /** @var int The starting line, relative to the structural element's location. */
    private $startingLine;

    /** @var int|null The number of lines, relative to the starting line. NULL means "to the end". */
    private $lineCount;

    /**
     * @param int|string      $startingLine should be a to int convertible value
     * @param int|string|null $lineCount    should be a to int convertible value
     */
    public function __construct($startingLine, $lineCount = null, ?Description $description = null)
    {
        Assert::integerish($startingLine);
        Assert::nullOrIntegerish($lineCount);

        $this->startingLine = (int) $startingLine;
        $this->lineCount    = $lineCount !== null ? (int) $lineCount : null;
        $this->description  = $description;
    }

    public static function create(
        string $body,
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
        Assert::stringNotEmpty($body);
        Assert::notNull($descriptionFactory);

        $startingLine = 1;
        $lineCount    = null;
        $description  = null;

        // Starting line / Number of lines / Description
        if (preg_match('/^([1-9]\d*)\s*(?:((?1))\s+)?(.*)$/sux', $body, $matches)) {
            $startingLine = (int) $matches[1];
            if (isset($matches[2]) && $matches[2] !== '') {
                $lineCount = (int) $matches[2];
            }

            $description = $matches[3];
        }

<<<<<<< HEAD
<<<<<<< HEAD
        return new static($startingLine, $lineCount, $descriptionFactory->create($description ?? '', $context));
=======
        return new static($startingLine, $lineCount, $descriptionFactory->create($description??'', $context));
>>>>>>> 22c0e54 (table changes)
=======
        return new static($startingLine, $lineCount, $descriptionFactory->create($description ?? '', $context));
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Gets the starting line.
     *
     * @return int The starting line, relative to the structural element's
     *     location.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getStartingLine(): int
=======
    public function getStartingLine() : int
>>>>>>> 22c0e54 (table changes)
=======
    public function getStartingLine(): int
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->startingLine;
    }

    /**
     * Returns the number of lines.
     *
     * @return int|null The number of lines, relative to the starting line. NULL
     *     means "to the end".
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getLineCount(): ?int
=======
    public function getLineCount() : ?int
>>>>>>> 22c0e54 (table changes)
=======
    public function getLineCount(): ?int
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->lineCount;
    }

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

        $startingLine = (string) $this->startingLine;

<<<<<<< HEAD
<<<<<<< HEAD
        $lineCount = $this->lineCount !== null ? ' ' . $this->lineCount : '';

        return $startingLine
            . $lineCount
            . ($description !== ''
                ? ' ' . $description
=======
        $lineCount = $this->lineCount !== null ? '' . $this->lineCount : '';
=======
        $lineCount = $this->lineCount !== null ? ' ' . $this->lineCount : '';
>>>>>>> f330c64 (optimization in progress)

        return $startingLine
            . $lineCount
            . ($description !== ''
<<<<<<< HEAD
                ? ($startingLine || $startingLine === '0' || $lineCount !== '' ? ' ' : '') . $description
>>>>>>> 22c0e54 (table changes)
=======
                ? ' ' . $description
>>>>>>> f330c64 (optimization in progress)
                : '');
    }
}
