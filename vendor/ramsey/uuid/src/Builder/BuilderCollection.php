<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Builder;

use Ramsey\Collection\AbstractCollection;
<<<<<<< HEAD
<<<<<<< HEAD
=======
use Ramsey\Collection\CollectionInterface;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Traversable;

/**
 * A collection of UuidBuilderInterface objects
<<<<<<< HEAD
<<<<<<< HEAD
 *
 * @extends AbstractCollection<UuidBuilderInterface>
 */
class BuilderCollection extends AbstractCollection
=======
 */
class BuilderCollection extends AbstractCollection implements CollectionInterface
>>>>>>> 22c0e54 (table changes)
=======
 *
 * @extends AbstractCollection<UuidBuilderInterface>
 */
class BuilderCollection extends AbstractCollection
>>>>>>> f330c64 (optimization in progress)
{
    public function getType(): string
    {
        return UuidBuilderInterface::class;
    }

    /**
     * @psalm-mutation-free
     * @psalm-suppress ImpureMethodCall
     * @psalm-suppress InvalidTemplateParam
     */
    public function getIterator(): Traversable
    {
        return parent::getIterator();
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into
     *     a UuidInterface instance
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
<<<<<<< HEAD
<<<<<<< HEAD
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, UuidBuilderInterface> $data */
=======
     */
    public function unserialize($serialized): void
    {
        /** @var mixed[] $data */
>>>>>>> 22c0e54 (table changes)
=======
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, UuidBuilderInterface> $data */
>>>>>>> f330c64 (optimization in progress)
        $data = unserialize($serialized, [
            'allowed_classes' => [
                BrickMathCalculator::class,
                GenericNumberConverter::class,
                GenericTimeConverter::class,
                GuidBuilder::class,
                NonstandardUuidBuilder::class,
                PhpTimeConverter::class,
                Rfc4122UuidBuilder::class,
            ],
        ]);

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        $this->data = array_filter(
            $data,
            function ($unserialized): bool {
                return $unserialized instanceof UuidBuilderInterface;
            }
        );
<<<<<<< HEAD
=======
        $this->data = $data;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    }
}
