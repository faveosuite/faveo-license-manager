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

namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;

/**
<<<<<<< HEAD
<<<<<<< HEAD
 * GenericNumberConverter uses the provided calculator to convert decimal
=======
 * GenericNumberConverter uses the provided calculate to convert decimal
>>>>>>> 22c0e54 (table changes)
=======
 * GenericNumberConverter uses the provided calculator to convert decimal
>>>>>>> f330c64 (optimization in progress)
 * numbers to and from hexadecimal values
 *
 * @psalm-immutable
 */
class GenericNumberConverter implements NumberConverterInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @psalm-return numeric-string
     * @psalm-suppress MoreSpecificReturnType we know that the retrieved `string` is never empty
     * @psalm-suppress LessSpecificReturnStatement we know that the retrieved `string` is never empty
     */
    public function fromHex(string $hex): string
    {
        return $this->calculator->fromBase($hex, 16)->toString();
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @psalm-return non-empty-string
     * @psalm-suppress MoreSpecificReturnType we know that the retrieved `string` is never empty
     * @psalm-suppress LessSpecificReturnStatement we know that the retrieved `string` is never empty
     */
    public function toHex(string $number): string
    {
<<<<<<< HEAD
<<<<<<< HEAD
        /** @phpstan-ignore-next-line PHPStan complains that this is not a non-empty-string. */
=======
>>>>>>> 22c0e54 (table changes)
=======
        /** @phpstan-ignore-next-line PHPStan complains that this is not a non-empty-string. */
>>>>>>> f330c64 (optimization in progress)
        return $this->calculator->toBase(new IntegerObject($number), 16);
    }
}
