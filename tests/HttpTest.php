<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    public function testIndexPageReturns200(): void
    {
        $client = new Client([
            'base_uri' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'),
            'timeout' => 5.0,
            'http_errors' => false,
        ]);

        $response = $client->get('/index.php');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Лабораторная работа №8', (string)$response->getBody());
    }
}