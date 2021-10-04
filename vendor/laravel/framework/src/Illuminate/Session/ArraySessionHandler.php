<?php

namespace Illuminate\Session;

use Illuminate\Support\InteractsWithTime;
<<<<<<< HEAD
<<<<<<< HEAD
=======
use ReturnTypeWillChange;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
use SessionHandlerInterface;

class ArraySessionHandler implements SessionHandlerInterface
{
    use InteractsWithTime;

    /**
     * The array of stored values.
     *
     * @var array
     */
    protected $storage = [];

    /**
     * The number of minutes the session should be valid.
     *
     * @var int
     */
    protected $minutes;

    /**
     * Create a new array driven handler instance.
     *
     * @param  int  $minutes
     * @return void
     */
    public function __construct($minutes)
    {
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
    #[ReturnTypeWillChange]
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
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
=======
     */
    #[ReturnTypeWillChange]
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
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
    #[ReturnTypeWillChange]
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return string|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function read($sessionId)
    {
        if (! isset($this->storage[$sessionId])) {
            return '';
        }

        $session = $this->storage[$sessionId];

        $expiration = $this->calculateExpiration($this->minutes * 60);

        if (isset($session['time']) && $session['time'] >= $expiration) {
            return $session['data'];
        }

        return '';
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
    #[ReturnTypeWillChange]
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function write($sessionId, $data)
    {
        $this->storage[$sessionId] = [
            'data' => $data,
            'time' => $this->currentTime(),
        ];

        return true;
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
    #[ReturnTypeWillChange]
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function destroy($sessionId)
    {
        if (isset($this->storage[$sessionId])) {
            unset($this->storage[$sessionId]);
        }

        return true;
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
    #[ReturnTypeWillChange]
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return int|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function gc($lifetime)
    {
        $expiration = $this->calculateExpiration($lifetime);

        foreach ($this->storage as $sessionId => $session) {
            if ($session['time'] < $expiration) {
                unset($this->storage[$sessionId]);
            }
        }

        return true;
    }

    /**
     * Get the expiration time of the session.
     *
     * @param  int  $seconds
     * @return int
     */
    protected function calculateExpiration($seconds)
    {
        return $this->currentTime() - $seconds;
    }
}
