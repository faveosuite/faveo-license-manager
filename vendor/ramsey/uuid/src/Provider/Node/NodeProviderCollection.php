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

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Collection\AbstractCollection;
<<<<<<< HEAD
<<<<<<< HEAD
=======
use Ramsey\Collection\CollectionInterface;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * A collection of NodeProviderInterface objects
<<<<<<< HEAD
<<<<<<< HEAD
 *
 * @extends AbstractCollection<NodeProviderInterface>
 */
class NodeProviderCollection extends AbstractCollection
=======
 */
class NodeProviderCollection extends AbstractCollection implements CollectionInterface
>>>>>>> 22c0e54 (table changes)
=======
 *
 * @extends AbstractCollection<NodeProviderInterface>
 */
class NodeProviderCollection extends AbstractCollection
>>>>>>> f330c64 (optimization in progress)
{
    public function getType(): string
    {
        return NodeProviderInterface::class;
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
        /** @var array<array-key, NodeProviderInterface> $data */
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
        /** @var array<array-key, NodeProviderInterface> $data */
>>>>>>> f330c64 (optimization in progress)
        $data = unserialize($serialized, [
            'allowed_classes' => [
                Hexadecimal::class,
                RandomNodeProvider::class,
                StaticNodeProvider::class,
                SystemNodeProvider::class,
            ],
        ]);

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        $this->data = array_filter(
            $data,
            function ($unserialized): bool {
                return $unserialized instanceof NodeProviderInterface;
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
