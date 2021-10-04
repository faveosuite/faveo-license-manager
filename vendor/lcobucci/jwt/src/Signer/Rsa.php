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

namespace Lcobucci\JWT\Signer;

use const OPENSSL_KEYTYPE_RSA;

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
/**
 * Base class for RSASSA-PKCS1 signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
<<<<<<< HEAD
abstract class Rsa extends OpenSSL
{
    final public function getKeyType()
=======
abstract class Rsa extends OpenSSL
{
    final public function sign(string $payload, Key $key): string
    {
        return $this->createSignature($key->contents(), $key->passphrase(), $payload);
    }

    final public function verify(string $expected, string $payload, Key $key): bool
    {
        return $this->verifySignature($expected, $payload, $key->contents());
    }

    final public function keyType(): int
>>>>>>> 22c0e54 (table changes)
=======
abstract class Rsa extends OpenSSL
{
    final public function getKeyType()
>>>>>>> f330c64 (optimization in progress)
    {
        return OPENSSL_KEYTYPE_RSA;
    }
}
