<?php

namespace Illuminate\Events;

class InvokeQueuedClosure
{
    /**
     * Handle the event.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  \Laravel\SerializableClosure\SerializableClosure  $closure
=======
     * @param  \Illuminate\Queue\SerializableClosure  $closure
>>>>>>> 22c0e54 (table changes)
=======
     * @param  \Laravel\SerializableClosure\SerializableClosure  $closure
>>>>>>> f330c64 (optimization in progress)
     * @param  array  $arguments
     * @return void
     */
    public function handle($closure, array $arguments)
    {
        call_user_func($closure->getClosure(), ...$arguments);
    }

    /**
     * Handle a job failure.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  \Laravel\SerializableClosure\SerializableClosure  $closure
=======
     * @param  \Illuminate\Queue\SerializableClosure  $closure
>>>>>>> 22c0e54 (table changes)
=======
     * @param  \Laravel\SerializableClosure\SerializableClosure  $closure
>>>>>>> f330c64 (optimization in progress)
     * @param  array  $arguments
     * @param  array  $catchCallbacks
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed($closure, array $arguments, array $catchCallbacks, $exception)
    {
        $arguments[] = $exception;

        collect($catchCallbacks)->each->__invoke(...$arguments);
    }
}
