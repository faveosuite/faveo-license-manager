<?php

namespace Tests\Unit\Backend\AFL;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class ConnectionControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_connection_whenConnectionIsEstablished_shouldRecieveResponse200()
    {
        $data = ['product_id' => 1,
            'refer' => 'https://www.faveo-helpdesk.com',
            'connection_hash' => '15e529b008655983e8cfcba8d39357914647347fb942ffdeac417e76f441c319',
        ];

        $response = $this->json('POST', url('api/ConnectionTest'), $data);
        $response->assertStatus(200);
    }

    public function test_connection_whenConnectionIsEstablishedWithWrongHash_shouldRecieveResponseFalse()
    {
        $data = ['product_id' => 1,
            'refer' => 'https://www.faveo-helpdesk.com',
            'connection_hash' => 'e9a2786da5c2eee1a5ca50c0eb1cb3659d084ac03ee13ac55575d97bbc20f159jdjsbdfdsj',
        ];

        $response = $this->json('POST', url('api/ConnectionTest'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.invalid_connection']);
    }

    public function test_connection_whenConnectionIsEstablishedWithNorefer_shouldRecieveResponseFalse()
    {
        $data = ['product_id' => 1,
            'connection_hash' => 'e9a2786da5c2eee1a5ca50c0eb1cb3659d084ac03ee13ac55575d97bbc20f159jdjsbdfdsj',
        ];

        $response = $this->json('POST', url('api/ConnectionTest'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.invalid_connection']);
    }
}
