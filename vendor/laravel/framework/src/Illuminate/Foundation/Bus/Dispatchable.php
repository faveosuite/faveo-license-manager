<?php

namespace Illuminate\Foundation\Bus;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Fluent;

trait Dispatchable
{
    /**
     * Dispatch the job with the given arguments.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public static function dispatch(...$arguments)
    {
        return new PendingDispatch(new static(...$arguments));
=======
    public static function dispatch()
    {
        return new PendingDispatch(new static(...func_get_args()));
>>>>>>> 22c0e54 (table changes)
=======
    public static function dispatch(...$arguments)
    {
        return new PendingDispatch(new static(...$arguments));
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Dispatch the job with the given arguments if the given truth test passes.
     *
     * @param  bool  $boolean
     * @param  mixed  ...$arguments
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent
     */
    public static function dispatchIf($boolean, ...$arguments)
    {
        return $boolean
            ? new PendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch the job with the given arguments unless the given truth test passes.
     *
     * @param  bool  $boolean
     * @param  mixed  ...$arguments
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent
     */
    public static function dispatchUnless($boolean, ...$arguments)
    {
        return ! $boolean
            ? new PendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @return mixed
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public static function dispatchSync(...$arguments)
    {
        return app(Dispatcher::class)->dispatchSync(new static(...$arguments));
=======
    public static function dispatchSync()
    {
        return app(Dispatcher::class)->dispatchSync(new static(...func_get_args()));
>>>>>>> 22c0e54 (table changes)
=======
    public static function dispatchSync(...$arguments)
    {
        return app(Dispatcher::class)->dispatchSync(new static(...$arguments));
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @return mixed
     *
     * @deprecated Will be removed in a future Laravel version.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public static function dispatchNow(...$arguments)
    {
        return app(Dispatcher::class)->dispatchNow(new static(...$arguments));
=======
    public static function dispatchNow()
    {
        return app(Dispatcher::class)->dispatchNow(new static(...func_get_args()));
>>>>>>> 22c0e54 (table changes)
=======
    public static function dispatchNow(...$arguments)
    {
        return app(Dispatcher::class)->dispatchNow(new static(...$arguments));
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Dispatch a command to its appropriate handler after the current process.
     *
     * @return mixed
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public static function dispatchAfterResponse(...$arguments)
    {
        return app(Dispatcher::class)->dispatchAfterResponse(new static(...$arguments));
=======
    public static function dispatchAfterResponse()
    {
        return app(Dispatcher::class)->dispatchAfterResponse(new static(...func_get_args()));
>>>>>>> 22c0e54 (table changes)
=======
    public static function dispatchAfterResponse(...$arguments)
    {
        return app(Dispatcher::class)->dispatchAfterResponse(new static(...$arguments));
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return \Illuminate\Foundation\Bus\PendingChain
     */
    public static function withChain($chain)
    {
        return new PendingChain(static::class, $chain);
    }
}
