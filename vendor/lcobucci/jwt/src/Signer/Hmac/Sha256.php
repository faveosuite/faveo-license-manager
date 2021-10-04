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

namespace Lcobucci\JWT\Signer\Hmac;

use Lcobucci\JWT\Signer\Hmac;

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
/**
 * Signer for HMAC SHA-256
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
class Sha256 extends Hmac
<<<<<<< HEAD
{
    /**
     * {@inheritdoc}
     */
    public function getAlgorithmId()
=======
final class Sha256 extends Hmac
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
        return 'HS256';
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
    public function algorithm(): string
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        return 'sha256';
    }
}
