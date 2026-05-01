<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/db.php';
require __DIR__ . '/QueueManager.php';

$q = new QueueManager();

echo "Рабочий запущен (RabbitMQ)\n";

$q->consume(function ($data) {
    echo "Получено сообщение: " . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    sleep(2);

    file_put_contents(
        __DIR__ . '/processed_rabbit.log',
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND
    );

    echo "ОБРАБОТАНО\n";
});

$q->close();