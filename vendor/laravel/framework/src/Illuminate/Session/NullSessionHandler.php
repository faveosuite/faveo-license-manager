<?php

namespace Illuminate\Session;

<<<<<<< HEAD
<<<<<<< HEAD
=======
use ReturnTypeWillChange;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
use SessionHandlerInterface;

class NullSessionHandler implements SessionHandlerInterface
{
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
        return true;
    }
}
