<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer\Key;

use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Key;
<<<<<<< HEAD
<<<<<<< HEAD

use function base64_decode;

final class InMemory extends Key
{
    /**
     * @param string $contents
     * @param string $passphrase
     *
     * @return self
     */
    public static function plainText($contents, $passphrase = '')
=======
use SplFileObject;
use Throwable;
=======
>>>>>>> f330c64 (optimization in progress)

use function base64_decode;

final class InMemory extends Key
{
<<<<<<< HEAD
    private string $contents;
    private string $passphrase;

    private function __construct(string $contents, string $passphrase)
    {
        $this->contents   = $contents;
        $this->passphrase = $passphrase;
    }

    public static function empty(): self
    {
        return new self('', '');
    }

    public static function plainText(string $contents, string $passphrase = ''): self
>>>>>>> 22c0e54 (table changes)
=======
    /**
     * @param string $contents
     * @param string $passphrase
     *
     * @return self
     */
    public static function plainText($contents, $passphrase = '')
>>>>>>> f330c64 (optimization in progress)
    {
        return new self($contents, $passphrase);
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param string $contents
     * @param string $passphrase
     *
     * @return self
     */
    public static function base64Encoded($contents, $passphrase = '')
<<<<<<< HEAD
=======
    public static function base64Encoded(string $contents, string $passphrase = ''): self
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    {
        $decoded = base64_decode($contents, true);

        if ($decoded === false) {
            throw CannotDecodeContent::invalidBase64String();
        }

        return new self($decoded, $passphrase);
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param string $path
     * @param string $passphrase
     *
     * @return InMemory
     *
     * @throws FileCouldNotBeRead
     */
    public static function file($path, $passphrase = '')
<<<<<<< HEAD
    {
        return new self('file://' . $path, $passphrase);
=======
    /** @throws FileCouldNotBeRead */
    public static function file(string $path, string $passphrase = ''): self
    {
        try {
            $file = new SplFileObject($path);
        } catch (Throwable $exception) {
            throw FileCouldNotBeRead::onPath($path, $exception);
        }

        $contents = $file->fread($file->getSize());
        assert(is_string($contents));

        return new self($contents, $passphrase);
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function passphrase(): string
    {
        return $this->passphrase;
>>>>>>> 22c0e54 (table changes)
=======
    {
        return new self('file://' . $path, $passphrase);
>>>>>>> f330c64 (optimization in progress)
    }
}
