<?php

use common\MovingFiles;
use common\RabbitMQ;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

require 'vendor/autoload.php';

echo json_encode((new MovingFiles(new RabbitMQ))->publish());