<?php

require_once __DIR__ . '/../www/VisitRepository.php';

use PHPUnit\Framework\TestCase;

class VisitRepositoryTest extends TestCase
{
    private PDO $pdoMock;
    private VisitRepository $repository;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->repository = new VisitRepository($this->pdoMock);
    }

    public function testAdd(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO visits (visit_date, source, page, duration) VALUES (:visit_date, :source, :page, :duration)')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->with([
                'visit_date' => '2026-03-29',
                'source' => 'Google',
                'page' => '/home',
                'duration' => 120,
            ])
            ->willReturn(true);

        $result = $this->repository->add('2026-03-29', 'Google', '/home', 120);

        $this->assertSame('Visit Google added', $result);
    }

    public function testGetAll(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);

        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT id, visit_date, source, page, duration FROM visits ORDER BY id DESC')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'visit_date' => '2026-03-01',
                    'source' => 'Google',
                    'page' => '/home',
                    'duration' => 120,
                ],
            ]);

        $result = $this->repository->getAll();

        $this->assertCount(1, $result);
        $this->assertSame('Google', $result[0]['source']);
    }
}