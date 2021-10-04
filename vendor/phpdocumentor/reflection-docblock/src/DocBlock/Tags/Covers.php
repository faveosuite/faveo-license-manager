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

/**
 * Reflection class for a @covers tag in a Docblock.
 */
final class Covers extends BaseTag implements Factory\StaticMethod
{
    /** @var string */
    protected $name = 'covers';

    /** @var Fqsen */
    private $refers;

    /**
     * Initializes this tag.
     */
    public function __construct(Fqsen $refers, ?Description $description = null)
    {
        $this->refers      = $refers;
        $this->description = $description;
    }

    public static function create(
        string $body,
        ?DescriptionFactory $descriptionFactory = null,
        ?FqsenResolver $resolver = null,
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
        Assert::notNull($resolver);

        $parts = Utils::pregSplit('/\s+/Su', $body, 2);

        return new static(
            self::resolveFqsen($parts[0], $resolver, $context),
            $descriptionFactory->create($parts[1] ?? '', $context)
        );
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
     * Returns the structural element this tag refers to.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getReference(): Fqsen
=======
    public function getReference() : Fqsen
>>>>>>> 22c0e54 (table changes)
=======
    public function getReference(): Fqsen
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
