<?php

# запись о визите
class VisitRecord
{
    public function __construct(
        public int $id,
        public string $visit_date,
        public string $source,
        public string $page,
        public int $duration
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'visit_date' => $this->visit_date,
            'source' => $this->source,
            'page' => $this->page,
            'duration' => $this->duration,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int)($data['id'] ?? 0),
            (string)($data['visit_date'] ?? ''),
            (string)($data['source'] ?? ''),
            (string)($data['page'] ?? ''),
            (int)($data['duration'] ?? 0)
        );
    }

    public function isLongVisit(int $threshold = 120): bool
    {
        return $this->duration >= $threshold;
    }
}