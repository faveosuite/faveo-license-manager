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

use Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter;
use Lcobucci\JWT\Signer\Ecdsa\SignatureConverter;
<<<<<<< HEAD
<<<<<<< HEAD
use const OPENSSL_KEYTYPE_EC;

/**
 * Base class for ECDSA signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
abstract class Ecdsa extends OpenSSL
{
    /**
     * @var SignatureConverter
     */
    private $converter;

    public function __construct(SignatureConverter $converter = null)
    {
        $this->converter = $converter ?: new MultibyteStringConverter();
    }

    /**
     * {@inheritdoc}
     */
    public function createHash($payload, Key $key)
    {
        return $this->converter->fromAsn1(
            parent::createHash($payload, $key),
            $this->getKeyLength()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function doVerify($expected, $payload, Key $key)
    {
        return parent::doVerify(
            $this->converter->toAsn1($expected, $this->getKeyLength()),
            $payload,
            $key
        );
    }

=======

=======
>>>>>>> f330c64 (optimization in progress)
use const OPENSSL_KEYTYPE_EC;

/**
 * Base class for ECDSA signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
abstract class Ecdsa extends OpenSSL
{
    /**
     * @var SignatureConverter
     */
    private $converter;

    public function __construct(SignatureConverter $converter = null)
    {
        $this->converter = $converter ?: new MultibyteStringConverter();
    }

    /**
     * {@inheritdoc}
     */
    public function createHash($payload, Key $key)
    {
        return $this->converter->fromAsn1(
            parent::createHash($payload, $key),
            $this->getKeyLength()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function doVerify($expected, $payload, Key $key)
    {
        return parent::doVerify(
            $this->converter->toAsn1($expected, $this->getKeyLength()),
            $payload,
            $key
        );
    }

<<<<<<< HEAD
    final public function keyType(): int
    {
        return OPENSSL_KEYTYPE_EC;
    }

>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * Returns the length of each point in the signature, so that we can calculate and verify R and S points properly
     *
     * @internal
     */
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    abstract public function getKeyLength();

    /**
     * {@inheritdoc}
     */
    final public function getKeyType()
    {
        return OPENSSL_KEYTYPE_EC;
    }
<<<<<<< HEAD
=======
    abstract public function keyLength(): int;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
}
