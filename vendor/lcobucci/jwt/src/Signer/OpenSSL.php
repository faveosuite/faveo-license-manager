<?php
<<<<<<< HEAD
<<<<<<< HEAD
namespace Lcobucci\JWT\Signer;

use InvalidArgumentException;
use function is_resource;
=======
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer;
use OpenSSLAsymmetricKey;

use function array_key_exists;
use function assert;
use function is_array;
use function is_bool;
use function is_string;
>>>>>>> 22c0e54 (table changes)
=======
namespace Lcobucci\JWT\Signer;

use InvalidArgumentException;
use function is_resource;
>>>>>>> f330c64 (optimization in progress)
use function openssl_error_string;
use function openssl_free_key;
use function openssl_pkey_get_details;
use function openssl_pkey_get_private;
use function openssl_pkey_get_public;
use function openssl_sign;
use function openssl_verify;

<<<<<<< HEAD
<<<<<<< HEAD
abstract class OpenSSL extends BaseSigner
{
    public function createHash($payload, Key $key)
    {
        $privateKey = $this->getPrivateKey($key->getContent(), $key->getPassphrase());
=======
abstract class OpenSSL implements Signer
{
    /**
     * @throws CannotSignPayload
     * @throws InvalidKeyProvided
     */
    final protected function createSignature(
        string $pem,
        string $passphrase,
        string $payload
    ): string {
        $key = $this->getPrivateKey($pem, $passphrase);
>>>>>>> 22c0e54 (table changes)
=======
abstract class OpenSSL extends BaseSigner
{
    public function createHash($payload, Key $key)
    {
        $privateKey = $this->getPrivateKey($key->getContent(), $key->getPassphrase());
>>>>>>> f330c64 (optimization in progress)

        try {
            $signature = '';

<<<<<<< HEAD
<<<<<<< HEAD
            if (! openssl_sign($payload, $signature, $privateKey, $this->getAlgorithm())) {
                throw CannotSignPayload::errorHappened(openssl_error_string());
=======
            if (! openssl_sign($payload, $signature, $key, $this->algorithm())) {
                $error = openssl_error_string();
                assert(is_string($error));

                throw CannotSignPayload::errorHappened($error);
>>>>>>> 22c0e54 (table changes)
=======
            if (! openssl_sign($payload, $signature, $privateKey, $this->getAlgorithm())) {
                throw CannotSignPayload::errorHappened(openssl_error_string());
>>>>>>> f330c64 (optimization in progress)
            }

            return $signature;
        } finally {
<<<<<<< HEAD
<<<<<<< HEAD
            openssl_free_key($privateKey);
=======
            $this->freeKey($key);
>>>>>>> 22c0e54 (table changes)
=======
            openssl_free_key($privateKey);
>>>>>>> f330c64 (optimization in progress)
        }
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * @param string $pem
     * @param string $passphrase
     *
     * @return resource
     */
    private function getPrivateKey($pem, $passphrase)
=======
     * @return resource|OpenSSLAsymmetricKey
=======
     * @param string $pem
     * @param string $passphrase
>>>>>>> f330c64 (optimization in progress)
     *
     * @return resource
     */
<<<<<<< HEAD
    private function getPrivateKey(string $pem, string $passphrase)
>>>>>>> 22c0e54 (table changes)
=======
    private function getPrivateKey($pem, $passphrase)
>>>>>>> f330c64 (optimization in progress)
    {
        $privateKey = openssl_pkey_get_private($pem, $passphrase);
        $this->validateKey($privateKey);

        return $privateKey;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /**
     * @param $expected
     * @param $payload
     * @param $key
=======
    /**
     * @param $expected
     * @param $payload
     * @param $pem
>>>>>>> f330c64 (optimization in progress)
     * @return bool
     */
    public function doVerify($expected, $payload, Key $key)
    {
        $publicKey = $this->getPublicKey($key->getContent());
        $result    = openssl_verify($payload, $expected, $publicKey, $this->getAlgorithm());
        openssl_free_key($publicKey);
<<<<<<< HEAD
=======
    /** @throws InvalidKeyProvided */
    final protected function verifySignature(
        string $expected,
        string $payload,
        string $pem
    ): bool {
        $key    = $this->getPublicKey($pem);
        $result = openssl_verify($payload, $expected, $key, $this->algorithm());
        $this->freeKey($key);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

        return $result === 1;
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * @param string $pem
     *
     * @return resource
     */
    private function getPublicKey($pem)
=======
     * @return resource|OpenSSLAsymmetricKey
=======
     * @param string $pem
>>>>>>> f330c64 (optimization in progress)
     *
     * @return resource
     */
<<<<<<< HEAD
    private function getPublicKey(string $pem)
>>>>>>> 22c0e54 (table changes)
=======
    private function getPublicKey($pem)
>>>>>>> f330c64 (optimization in progress)
    {
        $publicKey = openssl_pkey_get_public($pem);
        $this->validateKey($publicKey);

        return $publicKey;
    }

    /**
     * Raises an exception when the key type is not the expected type
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param resource|bool $key
     *
     * @throws InvalidArgumentException
     */
    private function validateKey($key)
    {
        if (! is_resource($key)) {
            throw InvalidKeyProvided::cannotBeParsed(openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);

        if (! isset($details['key']) || $details['type'] !== $this->getKeyType()) {
=======
     * @param resource|OpenSSLAsymmetricKey|bool $key
=======
     * @param resource|bool $key
>>>>>>> f330c64 (optimization in progress)
     *
     * @throws InvalidArgumentException
     */
    private function validateKey($key)
    {
        if (! is_resource($key)) {
            throw InvalidKeyProvided::cannotBeParsed(openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);

<<<<<<< HEAD
        if (! array_key_exists('key', $details) || $details['type'] !== $this->keyType()) {
>>>>>>> 22c0e54 (table changes)
=======
        if (! isset($details['key']) || $details['type'] !== $this->getKeyType()) {
>>>>>>> f330c64 (optimization in progress)
            throw InvalidKeyProvided::incompatibleKey();
        }
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
    /** @param resource|OpenSSLAsymmetricKey $key */
    private function freeKey($key): void
    {
        if ($key instanceof OpenSSLAsymmetricKey) {
            return;
        }

        openssl_free_key($key); // Deprecated and no longer necessary as of PHP >= 8.0
    }

>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * Returns the type of key to be used to create/verify the signature (using OpenSSL constants)
     *
     * @internal
     */
<<<<<<< HEAD
<<<<<<< HEAD
    abstract public function getKeyType();
=======
    abstract public function keyType(): int;
>>>>>>> 22c0e54 (table changes)
=======
    abstract public function getKeyType();
>>>>>>> f330c64 (optimization in progress)

    /**
     * Returns which algorithm to be used to create/verify the signature (using OpenSSL constants)
     *
     * @internal
     */
<<<<<<< HEAD
<<<<<<< HEAD
    abstract public function getAlgorithm();
=======
    abstract public function algorithm(): int;
>>>>>>> 22c0e54 (table changes)
=======
    abstract public function getAlgorithm();
>>>>>>> f330c64 (optimization in progress)
}
