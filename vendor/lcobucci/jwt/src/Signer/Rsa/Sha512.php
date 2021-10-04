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

namespace Lcobucci\JWT\Signer\Rsa;

use Lcobucci\JWT\Signer\Rsa;

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
/**
 * Signer for RSA SHA-512
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
class Sha512 extends Rsa
<<<<<<< HEAD
{
    /**
     * {@inheritdoc}
     */
    public function getAlgorithmId()
=======
use const OPENSSL_ALGO_SHA512;

final class Sha512 extends Rsa
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
        return 'RS512';
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
=======
    public function algorithm(): int
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return OPENSSL_ALGO_SHA512;
    }
}
