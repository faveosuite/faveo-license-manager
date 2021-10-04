<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer\Key;

use Lcobucci\JWT\Signer\Key;

use function file_exists;
use function strpos;
use function substr;

<<<<<<< HEAD
<<<<<<< HEAD
/** @deprecated Use \Lcobucci\JWT\Signer\Key\InMemory::file() instead */
final class LocalFileReference extends Key
{
    const PATH_PREFIX = 'file://';

    /**
     * @param string $path
     * @param string $passphrase
     *
     * @return self
     *
     * @throws FileCouldNotBeRead
     */
    public static function file($path, $passphrase = '')
=======
final class LocalFileReference implements Key
{
    private const PATH_PREFIX = 'file://';

    private string $path;
    private string $passphrase;

    private function __construct(string $path, string $passphrase)
    {
        $this->path       = $path;
        $this->passphrase = $passphrase;
    }

    /** @throws FileCouldNotBeRead */
    public static function file(string $path, string $passphrase = ''): self
>>>>>>> 22c0e54 (table changes)
=======
final class LocalFileReference extends Key
{
    const PATH_PREFIX = 'file://';

    /**
     * @param string $path
     * @param string $passphrase
     *
     * @return self
     *
     * @throws FileCouldNotBeRead
     */
    public static function file($path, $passphrase = '')
>>>>>>> f330c64 (optimization in progress)
    {
        if (strpos($path, self::PATH_PREFIX) === 0) {
            $path = substr($path, 7);
        }

<<<<<<< HEAD
        return new self(self::PATH_PREFIX . $path, $passphrase);
=======
        if (! file_exists($path)) {
            throw FileCouldNotBeRead::onPath($path);
        }

        $key = new self('', $passphrase);
        $key->content = self::PATH_PREFIX . $path;

<<<<<<< HEAD
    public function contents(): string
    {
        return self::PATH_PREFIX . $this->path;
    }

    public function passphrase(): string
    {
        return $this->passphrase;
>>>>>>> 22c0e54 (table changes)
=======
        return $key;
>>>>>>> f330c64 (optimization in progress)
    }
}
