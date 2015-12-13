<?php
/**
 * Created by PhpStorm.
 * User: zhanat
 * Date: 12/13/15
 * Time: 4:42 PM
 */

namespace common;

use PhpAmqpLib\Channel\AMQPChannel;
use Exception;

class MovingFiles implements MovingFilesInterface
{

    const PATH_FROM = 'public/from/';
    const PATH_TO = 'public/to/';

    /** @var RabbitMQ */
    private $rabbitMQ;

    public function __construct($rabbitMQ)
    {
        $this->rabbitMQ = $rabbitMQ;
    }

    public function publish()
    {
        try {
            $files = $this->getFiles();
            if (!$files) {
                return ['status' => 'empty', 'msg' => 'no files for moving'];
            }
            $this->sendMessages($files);
            return ['status' => 'ok'];
        } catch (Exception $e) {
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }

    private function sendMessages($files)
    {
        $connection = $this->rabbitMQ->getConnection();
        /** @var AMQPChannel $channel */
        $channel = $connection->channel();
        /*
         * прямая точка обмена
         * name: $exchange
         * type: direct
         * passive: false
         * durable: true // the exchange will survive server restarts
         * auto_delete: false // the exchange won't be deleted once the channel is closed.
         */
        $channel->exchange_declare(RabbitMQ::EXCHANGE_MOVING_FILES, RabbitMQ::EXCHANGE_TYPE_DIRECT, false, true, false);
        for($i = 0; $i < count($files) - 1; ++$i) {
            $msg = $this->rabbitMQ->createMessage(array_merge($files[$i], ['sumFilesSize' => $files['sumFilesSize'],
                'date' => date('d/m/Y (H:i:s)')
            ]));
            $channel->basic_publish($msg, RabbitMQ::EXCHANGE_MOVING_FILES);
        }
        $channel->close();
        $connection->close();
    }

    public function subscribe()
    {
        $connection = $this->rabbitMQ->getConnection();
        /** @var AMQPChannel $channel */
        $channel = $connection->channel();
        /*
         * очередь
         * name: $queue
         * passive: false
         * durable: true // the queue will survive server restarts
         * exclusive: false // the queue can be accessed in other channels
         * auto_delete: false //the queue won't be deleted once the channel is closed.
         */
        $channel->queue_declare(RabbitMQ::QUEUE_MOVING_FILES, false, true, false, false);
        /*
         * связать между собой очередь и точку обмена
         */
        $channel->queue_bind(RabbitMQ::QUEUE_MOVING_FILES, RabbitMQ::EXCHANGE_MOVING_FILES);

        $callback = function ($msg) use ($connection) {
            $data = json_decode($msg->body);
            $newMessage = $this->rabbitMQ->createMessage(['size' => $data->size, 'sumFilesSize' => $data->sumFilesSize]);
            /** @var AMQPChannel $channel */
            $channel = $connection->channel();
            $channel->exchange_declare(RabbitMQ::EXCHANGE_AFTER_MOVING_FILE, RabbitMQ::EXCHANGE_TYPE_DIRECT,
                false, true, false);
            $channel->basic_publish($newMessage, RabbitMQ::EXCHANGE_AFTER_MOVING_FILE);
            $channel->close();
            rename($data->from, $data->to);
            sleep(1);
        };

        $channel->basic_consume(RabbitMQ::QUEUE_MOVING_FILES, '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function getFiles()
    {
        if (!is_dir(self::PATH_FROM)) {
            throw new Exception(sprintf("directory <<%s>> not exist", self::PATH_FROM), 500);
        }
        $files = scandir(self::PATH_FROM);
        $res = [];
        $sumFilesSize = 0;
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $sumFilesSize += $fileSize = filesize(self::PATH_FROM . $file);
            $res[] = [
                'from' => self::PATH_FROM . $file,
                'to' => self::PATH_TO . $file,
                'size' => $fileSize,
            ];
        }
        if (!$res) {
            return;
        }
        $res['sumFilesSize'] = $sumFilesSize;
        return $res;
    }

}