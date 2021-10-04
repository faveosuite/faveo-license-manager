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

namespace Ramsey\Uuid\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
use ValueError;

use function ctype_xdigit;
use function sprintf;
<<<<<<< HEAD
=======

use function ctype_xdigit;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
use function strpos;
use function strtolower;
use function substr;

/**
 * A value object representing a hexadecimal number
 *
 * This class exists for type-safety purposes, to ensure that hexadecimal numbers
 * returned from ramsey/uuid methods as strings are truly hexadecimal and not some
 * other kind of string.
 *
 * @psalm-immutable
 */
final class Hexadecimal implements TypeInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value The hexadecimal value to store
     */
    public function __construct(string $value)
    {
        $value = strtolower($value);

        if (strpos($value, '0x') === 0) {
            $value = substr($value, 2);
        }

        if (!ctype_xdigit($value)) {
            throw new InvalidArgumentException(
                'Value must be a hexadecimal number'
            );
        }

        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function serialize(): string
    {
        return $this->toString();
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * @return array{string: string}
     */
    public function __serialize(): array
    {
        return ['string' => $this->toString()];
    }

    /**
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     * Constructs the object from a serialized string representation
     *
     * @param string $serialized The serialized string representation of the object
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
<<<<<<< HEAD
<<<<<<< HEAD
     * @psalm-suppress UnusedMethodCall
=======
>>>>>>> 22c0e54 (table changes)
=======
     * @psalm-suppress UnusedMethodCall
>>>>>>> f330c64 (optimization in progress)
     */
    public function unserialize($serialized): void
    {
        $this->__construct($serialized);
    }
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)

    /**
     * @param array{string: string} $data
     */
    public function __unserialize(array $data): void
    {
        // @codeCoverageIgnoreStart
        if (!isset($data['string'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        // @codeCoverageIgnoreEnd

        $this->unserialize($data['string']);
    }
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
}
