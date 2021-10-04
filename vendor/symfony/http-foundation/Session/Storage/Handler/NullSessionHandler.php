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

/**
 * Can be used in unit testing or in a situations where persisted sessions are not desired.
 *
 * @author Drak <drak@zikula.org>
 */
class NullSessionHandler extends AbstractSessionHandler
{
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        return true;
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function validateId($sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead(string $sessionId)
    {
        return '';
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function updateTimestamp($sessionId, $data)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite(string $sessionId, string $data)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDestroy(string $sessionId)
    {
        return true;
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * @return int|false
=======
     * @return bool
>>>>>>> 22c0e54 (table changes)
=======
     * @return int|false
>>>>>>> f330c64 (optimization in progress)
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        return 0;
=======
        return true;
>>>>>>> 22c0e54 (table changes)
=======
        return 0;
>>>>>>> f330c64 (optimization in progress)
    }
}
