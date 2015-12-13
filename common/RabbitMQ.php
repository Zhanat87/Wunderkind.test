<?php

namespace common;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{

    const EXCHANGE_MOVING_FILES = 'moving_files';
    const QUEUE_MOVING_FILES = 'moving_files';

    const EXCHANGE_AFTER_MOVING_FILE = 'after_moving_file';

    const EXCHANGE_TYPE_FANOUT = 'fanout';
    const EXCHANGE_TYPE_DIRECT = 'direct';
    const EXCHANGE_TYPE_TOPIC = 'topic';

    private static $connection;

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection()
    {
        if (null === self::$connection) {
            /** @var AMQPStreamConnection instance */
            self::$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        }
        return self::$connection;
    }

    public function createMessage($data)
    {
        return new AMQPMessage(json_encode($data), [
            'delivery_mode' => 2,
            'content_type' => 'application/json',
        ]);
    }

} 