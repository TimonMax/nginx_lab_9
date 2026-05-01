<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager
{
    private AMQPStreamConnection $connection;
    private $channel;
    private string $queueName = 'lab7_queue';

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            RABBITMQ_HOST,
            RABBITMQ_PORT,
            RABBITMQ_USER,
            RABBITMQ_PASSWORD
        );

        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
    }

    public function publish(array $data): void
    {
        $msg = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ['delivery_mode' => 2]
        );

        $this->channel->basic_publish($msg, '', $this->queueName);
    }

    public function consume(callable $callback): void
    {
        $this->channel->basic_consume(
            $this->queueName,
            '',
            false,
            true,
            false,
            false,
            function ($msg) use ($callback) {
                $data = json_decode($msg->body, true);
                $callback($data);
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
