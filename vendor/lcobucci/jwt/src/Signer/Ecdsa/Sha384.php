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
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer\Ecdsa;

use Lcobucci\JWT\Signer\Ecdsa;

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
/**
 * Signer for ECDSA SHA-384
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
class Sha384 extends Ecdsa
<<<<<<< HEAD
{
    /**
     * {@inheritdoc}
     */
    public function getAlgorithmId()
=======
use const OPENSSL_ALGO_SHA384;

final class Sha384 extends Ecdsa
{
    public function algorithmId(): string
>>>>>>> 22c0e54 (table changes)
=======
{
    /**
     * {@inheritdoc}
     */
    public function getAlgorithmId()
>>>>>>> f330c64 (optimization in progress)
    {
        return 'ES384';
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * {@inheritdoc}
     */
    public function getAlgorithm()
<<<<<<< HEAD
    {
        return 'sha384';
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyLength()
=======
    public function algorithm(): int
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return 'sha384';
    }

<<<<<<< HEAD
    public function keyLength(): int
>>>>>>> 22c0e54 (table changes)
=======
    /**
     * {@inheritdoc}
     */
    public function getKeyLength()
>>>>>>> f330c64 (optimization in progress)
    {
        return 96;
    }
}
