<?php

class VisitRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function add(string $visitDate, string $source, string $page, int $duration): string
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO visits (visit_date, source, page, duration) VALUES (:visit_date, :source, :page, :duration)'
        );

        $stmt->execute([
            'visit_date' => $visitDate,
            'source' => $source,
            'page' => $page,
            'duration' => $duration,
        ]);

        return "Visit {$source} added";
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, visit_date, source, page, duration FROM visits ORDER BY id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}