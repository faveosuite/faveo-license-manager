<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Token;

use function array_key_exists;

final class DataSet
{
    /** @var array<string, mixed> */
<<<<<<< HEAD
<<<<<<< HEAD
    private $data;
    /** @var string */
    private $encoded;

    /**
     * @param array<string, mixed> $data
     * @param string               $encoded
     */
    public function __construct(array $data, $encoded)
=======
    private array $data;
    private string $encoded;

    /** @param mixed[] $data */
    public function __construct(array $data, string $encoded)
>>>>>>> 22c0e54 (table changes)
=======
    private $data;
    /** @var string */
    private $encoded;

    /**
     * @param array<string, mixed> $data
     * @param string               $encoded
     */
    public function __construct(array $data, $encoded)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->data    = $data;
        $this->encoded = $encoded;
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * @param string     $name
=======
>>>>>>> 22c0e54 (table changes)
=======
     * @param string     $name
>>>>>>> f330c64 (optimization in progress)
     * @param mixed|null $default
     *
     * @return mixed|null
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->data[$name] : $default;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
=======
    public function get(string $name, $default = null)
=======
    public function get($name, $default = null)
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->has($name) ? $this->data[$name] : $default;
    }

<<<<<<< HEAD
    public function has(string $name): bool
>>>>>>> 22c0e54 (table changes)
=======
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
>>>>>>> f330c64 (optimization in progress)
    {
        return array_key_exists($name, $this->data);
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return array<string, mixed> */
    public function all()
=======
    /** @return mixed[] */
    public function all(): array
>>>>>>> 22c0e54 (table changes)
=======
    /** @return array<string, mixed> */
    public function all()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->data;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return string */
    public function toString()
=======
    public function toString(): string
>>>>>>> 22c0e54 (table changes)
=======
    /** @return string */
    public function toString()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->encoded;
    }
}
