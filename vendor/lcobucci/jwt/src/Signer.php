<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */
<<<<<<< HEAD

namespace Lcobucci\JWT;

use InvalidArgumentException;
use Lcobucci\JWT\Signer\Key;

/**
 * Basic interface for token signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
=======
declare(strict_types=1);
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT;

use InvalidArgumentException;
use Lcobucci\JWT\Signer\Key;

<<<<<<< HEAD
>>>>>>> 22c0e54 (table changes)
=======
/**
 * Basic interface for token signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
>>>>>>> f330c64 (optimization in progress)
interface Signer
{
    /**
     * Returns the algorithm id
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return string
     */
    public function getAlgorithmId();

    /**
     * Apply changes on headers according with algorithm
     *
     * @param array $headers
     */
    public function modifyHeader(array &$headers);

    /**
     * Returns a signature for given data
     *
     * @param string $payload
     * @param Key|string $key
     *
     * @return Signature
     *
     * @throws InvalidArgumentException When given key is invalid
     */
    public function sign($payload, $key);
=======
=======
     *
     * @return string
>>>>>>> f330c64 (optimization in progress)
     */
    public function getAlgorithmId();

    /**
     * Apply changes on headers according with algorithm
     *
     * @param array $headers
     */
<<<<<<< HEAD
    public function sign(string $payload, Key $key): string;
>>>>>>> 22c0e54 (table changes)
=======
    public function modifyHeader(array &$headers);

    /**
     * Returns a signature for given data
     *
     * @param string $payload
     * @param Key|string $key
     *
     * @return Signature
     *
     * @throws InvalidArgumentException When given key is invalid
     */
    public function sign($payload, $key);
>>>>>>> f330c64 (optimization in progress)

    /**
     * Returns if the expected hash matches with the data and key
     *
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * @param string $expected
     * @param string $payload
     * @param Key|string $key
     *
     * @return boolean
     *
     * @throws InvalidArgumentException When given key is invalid
<<<<<<< HEAD
     */
    public function verify($expected, $payload, $key);
=======
     * @throws InvalidKeyProvided When issue key is invalid/incompatible.
     * @throws ConversionFailed   When signature could not be converted.
     */
    public function verify(string $expected, string $payload, Key $key): bool;
>>>>>>> 22c0e54 (table changes)
=======
     */
    public function verify($expected, $payload, $key);
>>>>>>> f330c64 (optimization in progress)
}
