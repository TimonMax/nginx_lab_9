<?php

# аналитика
class AnalyticsReport
{
    public function __construct(private array $records)
    {
        $this->records = array_values($this->records);
    }

    public function totalVisits(): int
    {
        return count($this->records);
    }

    public function averageDuration(): float
    {
        if ($this->records === []) {
            return 0.0;
        }

        $sum = 0;
        foreach ($this->records as $row) {
            $sum += (int)($row['duration'] ?? 0);
        }

        return round($sum / count($this->records), 2);
    }

    public function bySource(): array
    {
        $grouped = [];

        foreach ($this->records as $row) {
            $source = (string)($row['source'] ?? 'Unknown');
            $duration = (int)($row['duration'] ?? 0);

            if (!isset($grouped[$source])) {
                $grouped[$source] = [
                    'source' => $source,
                    'visits' => 0,
                    'duration_sum' => 0,
                ];
            }

            $grouped[$source]['visits']++;
            $grouped[$source]['duration_sum'] += $duration;
        }

        $result = [];
        foreach ($grouped as $source => $stats) {
            $result[] = [
                'source' => $source,
                'visits' => $stats['visits'],
                'avg_duration' => round($stats['duration_sum'] / $stats['visits'], 2),
            ];
        }

        usort($result, fn($a, $b) => $b['visits'] <=> $a['visits']);

        return $result;
    }

    public function recent(int $limit = 10): array
    {
        return array_slice(array_reverse($this->records), 0, $limit);
    }
}