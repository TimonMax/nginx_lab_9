<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db.php';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$demoEvents = [
    ['id' => 1, 'visit_date' => '2026-03-01', 'source' => 'Google',    'page' => '/home',      'duration' => 120],
    ['id' => 2, 'visit_date' => '2026-03-01', 'source' => 'Steam',     'page' => '/store',     'duration' => 95],
    ['id' => 3, 'visit_date' => '2026-03-02', 'source' => 'Google',    'page' => '/blog',      'duration' => 140],
    ['id' => 4, 'visit_date' => '2026-03-02', 'source' => 'Telegram',  'page' => '/promo',     'duration' => 60],
    ['id' => 5, 'visit_date' => '2026-03-03', 'source' => 'Yandex',    'page' => '/home',      'duration' => 110],
    ['id' => 6, 'visit_date' => '2026-03-03', 'source' => 'Google',    'page' => '/checkout',  'duration' => 180],
    ['id' => 7, 'visit_date' => '2026-03-04', 'source' => 'Steam',     'page' => '/charts',    'duration' => 75],
    ['id' => 8, 'visit_date' => '2026-03-04', 'source' => 'Referral',  'page' => '/pricing',   'duration' => 105],
    ['id' => 9, 'visit_date' => '2026-03-05', 'source' => 'Google',    'page' => '/catalog',   'duration' => 130],
    ['id' => 10,'visit_date' => '2026-03-05', 'source' => 'Telegram',  'page' => '/checkout',  'duration' => 210],
];

$totalVisits = count($demoEvents);
$avgDuration = round(array_sum(array_column($demoEvents, 'duration')) / $totalVisits, 2);

$bySource = [];
foreach ($demoEvents as $row) {
    $source = $row['source'];
    if (!isset($bySource[$source])) {
        $bySource[$source] = ['visits' => 0, 'duration_sum' => 0];
    }
    $bySource[$source]['visits']++;
    $bySource[$source]['duration_sum'] += $row['duration'];
}

$bySourceRows = [];
foreach ($bySource as $source => $stats) {
    $bySourceRows[] = [
        'source' => $source,
        'visits' => $stats['visits'],
        'avg_duration' => round($stats['duration_sum'] / $stats['visits'], 2),
    ];
}

usort($bySourceRows, fn($a, $b) => $b['visits'] <=> $a['visits']);

$recent = array_reverse($demoEvents);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>ЛР8 — RabbitMQ аналитика и тестирование</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        body { font-family: Arial, Helvetica, sans-serif; max-width: 1100px; margin: 0 auto; padding: 20px; background: #fff; color: #222; }
        .box { border: 1px solid #ddd; border-radius: 10px; padding: 14px; margin: 14px 0; background: #fafafa; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .metric { font-size: 28px; font-weight: bold; }
        .label { color: #666; font-size: 14px; margin-bottom: 6px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 6px 0 12px;
        }
        button {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #2d6cdf;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }
        .muted { color: #666; font-size: 14px; }
        code { background: #eee; padding: 2px 5px; border-radius: 4px; }
        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <h1>Лабораторная работа №8 — RabbitMQ и тестирование</h1>

    <div class="box">
        <div class="label">Схема работы</div>
        <div class="muted">
            Браузер → <code>send.php</code> → RabbitMQ очередь <code>lab7_queue</code> → <code>worker.php</code> → лог обработки.
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <div class="label">Событий в демо-выборке</div>
            <div class="metric"><?= e($totalVisits) ?></div>
        </div>
        <div class="box">
            <div class="label">Средняя длительность визита</div>
            <div class="metric"><?= e($avgDuration) ?></div>
        </div>
    </div>

    <div class="box">
        <h2>Отправить аналитическое событие в очередь</h2>
        <p class="muted">
            Эта форма демонстрирует producer: данные уходят в RabbitMQ, а обработку выполняет worker.
        </p>

        <form method="post" action="/send.php">
            <label>Дата визита</label>
            <input type="date" name="visit_date" value="<?= e(date('Y-m-d')) ?>" required>

            <label>Источник</label>
            <input type="text" name="source" placeholder="Google, Steam, Telegram..." required>

            <label>Страница</label>
            <input type="text" name="page" placeholder="/home" required>

            <label>Длительность, сек.</label>
            <input type="number" name="duration" min="0" value="120" required>

            <button type="submit">Отправить в очередь</button>
        </form>
    </div>

    <div class="box">
        <h2>Аналитика по источникам</h2>
        <table>
            <tr>
                <th>Источник</th>
                <th>Визиты</th>
                <th>Средняя длительность</th>
            </tr>
            <?php foreach ($bySourceRows as $row): ?>
                <tr>
                    <td><?= e($row['source']) ?></td>
                    <td><?= e($row['visits']) ?></td>
                    <td><?= e($row['avg_duration']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="box">
        <h2>Пример последних записей</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Источник</th>
                <th>Страница</th>
                <th>Длительность</th>
            </tr>
            <?php foreach ($recent as $row): ?>
                <tr>
                    <td><?= e($row['id']) ?></td>
                    <td><?= e($row['visit_date']) ?></td>
                    <td><?= e($row['source']) ?></td>
                    <td><?= e($row['page']) ?></td>
                    <td><?= e($row['duration']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>