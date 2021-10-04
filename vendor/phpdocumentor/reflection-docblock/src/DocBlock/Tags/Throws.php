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
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;

/**
 * Reflection class for a {@}throws tag in a Docblock.
 */
final class Throws extends TagWithType implements Factory\StaticMethod
{
    public function __construct(Type $type, ?Description $description = null)
    {
        $this->name        = 'throws';
        $this->type        = $type;
        $this->description = $description;
    }

    public static function create(
        string $body,
        ?TypeResolver $typeResolver = null,
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
        Assert::notNull($typeResolver);
        Assert::notNull($descriptionFactory);

        [$type, $description] = self::extractTypeFromBody($body);

        $type        = $typeResolver->resolve($type, $context);
        $description = $descriptionFactory->create($description, $context);

        return new static($type, $description);
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

        $type = (string) $this->type;

        return $type . ($description !== '' ? ($type !== '' ? ' ' : '') . $description : '');
    }
}
