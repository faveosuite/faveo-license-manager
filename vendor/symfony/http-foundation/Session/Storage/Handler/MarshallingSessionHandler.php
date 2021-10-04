<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use Symfony\Component\Cache\Marshaller\MarshallerInterface;

/**
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class MarshallingSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    private $handler;
    private $marshaller;

    public function __construct(AbstractSessionHandler $handler, MarshallerInterface $marshaller)
    {
        $this->handler = $handler;
        $this->marshaller = $marshaller;
    }

    /**
     * @return bool
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function open($savePath, $name)
    {
        return $this->handler->open($savePath, $name);
    }

    /**
     * @return bool
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function close()
    {
        return $this->handler->close();
    }

    /**
     * @return bool
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function destroy($sessionId)
    {
        return $this->handler->destroy($sessionId);
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * @return int|false
     */
    #[\ReturnTypeWillChange]
=======
     * @return bool
     */
>>>>>>> 22c0e54 (table changes)
=======
     * @return int|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function gc($maxlifetime)
    {
        return $this->handler->gc($maxlifetime);
    }

    /**
     * @return string
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function read($sessionId)
    {
        return $this->marshaller->unmarshall($this->handler->read($sessionId));
    }

    /**
     * @return bool
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function write($sessionId, $data)
    {
        $failed = [];
        $marshalledData = $this->marshaller->marshall(['data' => $data], $failed);

        if (isset($failed['data'])) {
            return false;
        }

        return $this->handler->write($sessionId, $marshalledData['data']);
    }

    /**
     * @return bool
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function validateId($sessionId)
    {
        return $this->handler->validateId($sessionId);
    }

    /**
     * @return bool
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function updateTimestamp($sessionId, $data)
    {
        return $this->handler->updateTimestamp($sessionId, $data);
    }
}
