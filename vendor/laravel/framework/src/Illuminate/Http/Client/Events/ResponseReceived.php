<?php

namespace Illuminate\Http\Client\Events;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;

class ResponseReceived
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Client\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \Illuminate\Http\Client\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  \Illuminate\Http\Client\Request  $request
=======
     * @param \Illuminate\Http\Client\Request $request
>>>>>>> 22c0e54 (table changes)
=======
     * @param  \Illuminate\Http\Client\Request  $request
>>>>>>> f330c64 (optimization in progress)
     * @param  \Illuminate\Http\Client\Response  $response
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
