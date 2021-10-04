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

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen as FqsenRef;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use phpDocumentor\Reflection\Utils;
use Webmozart\Assert\Assert;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use function array_key_exists;
use function explode;
use function preg_match;

/**
 * Reflection class for an {@}see tag in a Docblock.
 */
final class See extends BaseTag implements Factory\StaticMethod
{
    /** @var string */
    protected $name = 'see';

    /** @var Reference */
    protected $refers;

    /**
     * Initializes this tag.
     */
    public function __construct(Reference $refers, ?Description $description = null)
    {
        $this->refers      = $refers;
        $this->description = $description;
    }

    public static function create(
        string $body,
        ?FqsenResolver $typeResolver = null,
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
        Assert::notNull($descriptionFactory);

        $parts = Utils::pregSplit('/\s+/Su', $body, 2);
        $description = isset($parts[1]) ? $descriptionFactory->create($parts[1], $context) : null;

        // https://tools.ietf.org/html/rfc2396#section-3
<<<<<<< HEAD
<<<<<<< HEAD
        if (preg_match('#\w://\w#', $parts[0])) {
=======
        if (preg_match('/\w:\/\/\w/i', $parts[0])) {
>>>>>>> 22c0e54 (table changes)
=======
        if (preg_match('#\w://\w#', $parts[0])) {
>>>>>>> f330c64 (optimization in progress)
            return new static(new Url($parts[0]), $description);
        }

        return new static(new FqsenRef(self::resolveFqsen($parts[0], $typeResolver, $context)), $description);
    }

<<<<<<< HEAD
<<<<<<< HEAD
    private static function resolveFqsen(string $parts, ?FqsenResolver $fqsenResolver, ?TypeContext $context): Fqsen
=======
    private static function resolveFqsen(string $parts, ?FqsenResolver $fqsenResolver, ?TypeContext $context) : Fqsen
>>>>>>> 22c0e54 (table changes)
=======
    private static function resolveFqsen(string $parts, ?FqsenResolver $fqsenResolver, ?TypeContext $context): Fqsen
>>>>>>> f330c64 (optimization in progress)
    {
        Assert::notNull($fqsenResolver);
        $fqsenParts = explode('::', $parts);
        $resolved = $fqsenResolver->resolve($fqsenParts[0], $context);

        if (!array_key_exists(1, $fqsenParts)) {
            return $resolved;
        }

        return new Fqsen($resolved . '::' . $fqsenParts[1]);
    }

    /**
     * Returns the ref of this tag.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getReference(): Reference
=======
    public function getReference() : Reference
>>>>>>> 22c0e54 (table changes)
=======
    public function getReference(): Reference
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->refers;
    }

    /**
     * Returns a string representation of this tag.
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

        $refers = (string) $this->refers;

        return $refers . ($description !== '' ? ($refers !== '' ? ' ' : '') . $description : '');
    }
}
