<?php

use common\MovingFiles;
use common\RabbitMQ;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

require 'vendor/autoload.php';

try {
    (new MovingFiles(new RabbitMQ))->subscribe();
    echo "files success moving" . PHP_EOL;
} catch (PDOException $e) {
    echo $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}