<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../www/VisitRecord.php';

class VisitRecordTest extends TestCase
{
    public function testToArrayReturnsCorrectData(): void
    {
        $record = new VisitRecord(1, '2026-03-01', 'Google', '/home', 120);

        $this->assertSame([
            'id' => 1,
            'visit_date' => '2026-03-01',
            'source' => 'Google',
            'page' => '/home',
            'duration' => 120,
        ], $record->toArray());
    }

    public function testFromArrayCreatesObject(): void
    {
        $record = VisitRecord::fromArray([
            'id' => 2,
            'visit_date' => '2026-03-02',
            'source' => 'Telegram',
            'page' => '/promo',
            'duration' => 60,
        ]);

        $this->assertSame(2, $record->id);
        $this->assertSame('2026-03-02', $record->visit_date);
        $this->assertSame('Telegram', $record->source);
        $this->assertSame('/promo', $record->page);
        $this->assertSame(60, $record->duration);
    }

    public function testIsLongVisit(): void
    {
        $record = new VisitRecord(3, '2026-03-03', 'Yandex', '/home', 180);

        $this->assertTrue($record->isLongVisit());
    }
}