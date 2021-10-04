<?php

namespace Illuminate\Session;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use SessionHandlerInterface;

class CacheBasedSessionHandler implements SessionHandlerInterface
{
    /**
     * The cache repository instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The number of minutes to store the data in the cache.
     *
     * @var int
     */
    protected $minutes;

    /**
     * Create a new cache driven handler instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @param  int  $minutes
     * @return void
     */
    public function __construct(CacheContract $cache, $minutes)
    {
        $this->cache = $cache;
        $this->minutes = $minutes;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
<<<<<<< HEAD
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return string|false
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return string|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function read($sessionId)
    {
        return $this->cache->get($sessionId, '');
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function write($sessionId, $data)
    {
        return $this->cache->put($sessionId, $data, $this->minutes * 60);
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
<<<<<<< HEAD
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    public function destroy($sessionId)
    {
        return $this->cache->forget($sessionId);
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return int|false
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return int|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function gc($lifetime)
    {
        return true;
    }

    /**
     * Get the underlying cache repository.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getCache()
    {
        return $this->cache;
    }
}
