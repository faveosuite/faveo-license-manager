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

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Exception\RandomSourceException;
<<<<<<< HEAD
<<<<<<< HEAD
use Throwable;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Throwable;
>>>>>>> f330c64 (optimization in progress)

/**
 * RandomBytesGenerator generates strings of random binary data using the
 * built-in `random_bytes()` PHP function
 *
 * @link http://php.net/random_bytes random_bytes()
 */
class RandomBytesGenerator implements RandomGeneratorInterface
{
    /**
     * @throws RandomSourceException if random_bytes() throws an exception/error
     *
     * @inheritDoc
     */
    public function generate(int $length): string
    {
        try {
            return random_bytes($length);
<<<<<<< HEAD
<<<<<<< HEAD
        } catch (Throwable $exception) {
=======
        } catch (\Throwable $exception) {
>>>>>>> 22c0e54 (table changes)
=======
        } catch (Throwable $exception) {
>>>>>>> f330c64 (optimization in progress)
            throw new RandomSourceException(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }
}
