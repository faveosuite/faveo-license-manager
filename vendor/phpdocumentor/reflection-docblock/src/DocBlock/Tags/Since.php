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
 * Reflection class for a {@}since tag in a Docblock.
 */
final class Since extends BaseTag implements Factory\StaticMethod
{
    /** @var string */
    protected $name = 'since';

    /**
     * PCRE regular expression matching a version vector.
     * Assumes the "x" modifier.
     */
    public const REGEX_VECTOR = '(?:
        # Normal release vectors.
        \d\S*
        |
        # VCS version vectors. Per PHPCS, they are expected to
        # follow the form of the VCS name, followed by ":", followed
        # by the version vector itself.
        # By convention, popular VCSes like CVS, SVN and GIT use "$"
        # around the actual version vector.
        [^\s\:]+\:\s*\$[^\$]+\$
    )';

    /** @var string|null The version vector. */
    private $version;

    public function __construct(?string $version = null, ?Description $description = null)
    {
        Assert::nullOrNotEmpty($version);

        $this->version     = $version;
        $this->description = $description;
    }

    public static function create(
        ?string $body,
        ?DescriptionFactory $descriptionFactory = null,
        ?TypeContext $context = null
<<<<<<< HEAD
<<<<<<< HEAD
    ): ?self {
=======
    ) : ?self {
>>>>>>> 22c0e54 (table changes)
=======
    ): ?self {
>>>>>>> f330c64 (optimization in progress)
        if (empty($body)) {
            return new static();
        }

        $matches = [];
        if (!preg_match('/^(' . self::REGEX_VECTOR . ')\s*(.+)?$/sux', $body, $matches)) {
            return null;
        }

        Assert::notNull($descriptionFactory);

        return new static(
            $matches[1],
            $descriptionFactory->create($matches[2] ?? '', $context)
        );
    }

    /**
     * Gets the version section of the tag.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getVersion(): ?string
=======
    public function getVersion() : ?string
>>>>>>> 22c0e54 (table changes)
=======
    public function getVersion(): ?string
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->version;
    }

    /**
     * Returns a string representation for this tag.
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

        $version = (string) $this->version;

        return $version . ($description !== '' ? ($version !== '' ? ' ' : '') . $description : '');
    }
}
