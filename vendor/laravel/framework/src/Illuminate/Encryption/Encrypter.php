<?php

namespace Illuminate\Encryption;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\StringEncrypter;
use RuntimeException;

class Encrypter implements EncrypterContract, StringEncrypter
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher;

    /**
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * The supported cipher algorithms and their properties.
     *
     * @var array
     */
    private static $supportedCiphers = [
        'aes-128-cbc' => ['size' => 16, 'aead' => false],
        'aes-256-cbc' => ['size' => 32, 'aead' => false],
        'aes-128-gcm' => ['size' => 16, 'aead' => true],
        'aes-256-gcm' => ['size' => 32, 'aead' => true],
    ];

    /**
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return void
     *
     * @throws \RuntimeException
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function __construct($key, $cipher = 'aes-128-cbc')
    {
        $key = (string) $key;

        if (! static::supported($key, $cipher)) {
            $ciphers = implode(', ', array_keys(self::$supportedCiphers));

            throw new RuntimeException("Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}.");
        }

        $this->key = $key;
        $this->cipher = $cipher;
=======
    public function __construct($key, $cipher = 'AES-128-CBC')
=======
    public function __construct($key, $cipher = 'aes-128-cbc')
>>>>>>> f330c64 (optimization in progress)
    {
        $key = (string) $key;

        if (! static::supported($key, $cipher)) {
            $ciphers = implode(', ', array_keys(self::$supportedCiphers));

            throw new RuntimeException("Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}.");
        }
<<<<<<< HEAD
>>>>>>> 22c0e54 (table changes)
=======

        $this->key = $key;
        $this->cipher = $cipher;
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        if (! isset(self::$supportedCiphers[strtolower($cipher)])) {
            return false;
        }

        return mb_strlen($key, '8bit') === self::$supportedCiphers[strtolower($cipher)]['size'];
=======
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) ||
               ($cipher === 'AES-256-CBC' && $length === 32);
>>>>>>> 22c0e54 (table changes)
=======
        if (! isset(self::$supportedCiphers[strtolower($cipher)])) {
            return false;
        }

        return mb_strlen($key, '8bit') === self::$supportedCiphers[strtolower($cipher)]['size'];
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Create a new encryption key for the given cipher.
     *
     * @param  string  $cipher
     * @return string
     */
    public static function generateKey($cipher)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        return random_bytes(self::$supportedCiphers[strtolower($cipher)]['size'] ?? 32);
=======
        return random_bytes($cipher === 'AES-128-CBC' ? 16 : 32);
>>>>>>> 22c0e54 (table changes)
=======
        return random_bytes(self::$supportedCiphers[strtolower($cipher)]['size'] ?? 32);
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encrypt($value, $serialize = true)
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        $iv = random_bytes(openssl_cipher_iv_length(strtolower($this->cipher)));

        $tag = '';

        $value = self::$supportedCiphers[strtolower($this->cipher)]['aead']
            ? \openssl_encrypt(
                $serialize ? serialize($value) : $value,
                strtolower($this->cipher), $this->key, 0, $iv, $tag
            )
            : \openssl_encrypt(
                $serialize ? serialize($value) : $value,
                strtolower($this->cipher), $this->key, 0, $iv
            );
<<<<<<< HEAD
=======
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher, $this->key, 0, $iv
        );
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

<<<<<<< HEAD
<<<<<<< HEAD
        $iv = base64_encode($iv);
        $tag = base64_encode($tag);

        $mac = self::$supportedCiphers[strtolower($this->cipher)]['aead']
            ? '' // For AEAD-algoritms, the tag / MAC is returned by openssl_encrypt...
            : $this->hash($iv, $value);

        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);
=======
        // Once we get the encrypted value we'll go ahead and base64_encode the input
        // vector and create the MAC for the encrypted value so we can then verify
        // its authenticity. Then, we'll JSON the data into the "payload" array.
        $mac = $this->hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'), JSON_UNESCAPED_SLASHES);
>>>>>>> 22c0e54 (table changes)
=======
        $iv = base64_encode($iv);
        $tag = base64_encode($tag);

        $mac = self::$supportedCiphers[strtolower($this->cipher)]['aead']
            ? '' // For AEAD-algoritms, the tag / MAC is returned by openssl_encrypt...
            : $this->hash($iv, $value);

        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);
>>>>>>> f330c64 (optimization in progress)

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        $tag = empty($payload['tag']) ? null : base64_decode($payload['tag']);

        if (self::$supportedCiphers[strtolower($this->cipher)]['aead'] && strlen($tag) !== 16) {
            throw new DecryptException('Could not decrypt the data.');
        }

<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
<<<<<<< HEAD
<<<<<<< HEAD
            $payload['value'], strtolower($this->cipher), $this->key, 0, $iv, $tag ?? ''
=======
            $payload['value'], $this->cipher, $this->key, 0, $iv
>>>>>>> 22c0e54 (table changes)
=======
            $payload['value'], strtolower($this->cipher), $this->key, 0, $iv, $tag ?? ''
>>>>>>> f330c64 (optimization in progress)
        );

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $this->validPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

<<<<<<< HEAD
<<<<<<< HEAD
        if (! self::$supportedCiphers[strtolower($this->cipher)]['aead'] && ! $this->validMac($payload)) {
=======
        if (! $this->validMac($payload)) {
>>>>>>> 22c0e54 (table changes)
=======
        if (! self::$supportedCiphers[strtolower($this->cipher)]['aead'] && ! $this->validMac($payload)) {
>>>>>>> f330c64 (optimization in progress)
            throw new DecryptException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
<<<<<<< HEAD
<<<<<<< HEAD
            strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(strtolower($this->cipher));
=======
               strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
>>>>>>> 22c0e54 (table changes)
=======
            strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(strtolower($this->cipher));
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function validMac(array $payload)
    {
        return hash_equals(
            $this->hash($payload['iv'], $payload['value']), $payload['mac']
        );
    }

    /**
     * Get the encryption key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
