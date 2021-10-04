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

namespace Lcobucci\JWT\Signer;

/**
 * Base class for hmac signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
abstract class Hmac extends BaseSigner
{
    /**
     * {@inheritdoc}
     */
    public function createHash($payload, Key $key)
    {
        return hash_hmac($this->getAlgorithm(), $payload, $key->getContent(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function doVerify($expected, $payload, Key $key)
    {
        if (!is_string($expected)) {
            return false;
        }

        return hash_equals($expected, $this->createHash($payload, $key));
    }

    /**
     * Returns the algorithm name
     *
     * @internal
     *
     * @return string
     */
    abstract public function getAlgorithm();
=======
declare(strict_types=1);
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer;

/**
 * Base class for hmac signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
abstract class Hmac extends BaseSigner
{
    /**
     * {@inheritdoc}
     */
    public function createHash($payload, Key $key)
    {
        return hash_hmac($this->getAlgorithm(), $payload, $key->getContent(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function doVerify($expected, $payload, Key $key)
    {
        if (!is_string($expected)) {
            return false;
        }

        return hash_equals($expected, $this->createHash($payload, $key));
    }

<<<<<<< HEAD
    abstract public function algorithm(): string;
>>>>>>> 22c0e54 (table changes)
=======
    /**
     * Returns the algorithm name
     *
     * @internal
     *
     * @return string
     */
    abstract public function getAlgorithm();
>>>>>>> f330c64 (optimization in progress)
}
