<?php

namespace Illuminate\Http\Client;

<<<<<<< HEAD
<<<<<<< HEAD
use GuzzleHttp\Utils;

=======
>>>>>>> 22c0e54 (table changes)
=======
use GuzzleHttp\Utils;

>>>>>>> f330c64 (optimization in progress)
/**
 * @mixin \Illuminate\Http\Client\Factory
 */
class Pool
{
    /**
     * The factory instance.
     *
     * @var \Illuminate\Http\Client\Factory
     */
    protected $factory;

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * The handler function for the Guzzle client.
     *
     * @var callable
     */
    protected $handler;
=======
     * The client instance.
=======
     * The handler function for the Guzzle client.
>>>>>>> f330c64 (optimization in progress)
     *
     * @var callable
     */
<<<<<<< HEAD
    protected $client;
>>>>>>> 22c0e54 (table changes)
=======
    protected $handler;
>>>>>>> f330c64 (optimization in progress)

    /**
     * The pool of requests.
     *
     * @var array
     */
    protected $pool = [];

    /**
     * Create a new requests pool.
     *
     * @param  \Illuminate\Http\Client\Factory|null  $factory
     * @return void
     */
    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory ?: new Factory();

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        if (method_exists(Utils::class, 'chooseHandler')) {
            $this->handler = Utils::chooseHandler();
        } else {
            $this->handler = \GuzzleHttp\choose_handler();
        }
<<<<<<< HEAD
=======
        $this->client = $this->factory->buildClient();
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Add a request to the pool with a key.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function as(string $key)
    {
        return $this->pool[$key] = $this->asyncRequest();
    }

    /**
     * Retrieve a new async pending request.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function asyncRequest()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        return $this->factory->setHandler($this->handler)->async();
=======
        return $this->factory->setClient($this->client)->async();
>>>>>>> 22c0e54 (table changes)
=======
        return $this->factory->setHandler($this->handler)->async();
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Retrieve the requests in the pool.
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->pool;
    }

    /**
     * Add a request to the pool with a numeric index.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function __call($method, $parameters)
    {
        return $this->pool[] = $this->asyncRequest()->$method(...$parameters);
    }
}
