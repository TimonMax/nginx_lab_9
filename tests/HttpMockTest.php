<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class HttpMockTest extends TestCase
{
    public function testMockRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'OK')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $response = $client->get('/test');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', (string) $response->getBody());
    }
}