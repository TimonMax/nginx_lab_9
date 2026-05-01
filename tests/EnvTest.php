<?php

use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testTestEnvIsLoaded(): void
    {
        $this->assertSame('db', $_ENV['DB_HOST'] ?? null);
        $this->assertSame('test_db', $_ENV['DB_NAME'] ?? null);
        $this->assertSame('test_user', $_ENV['DB_USER'] ?? null);
        $this->assertSame('test_pass', $_ENV['DB_PASSWORD'] ?? null);
    }
}