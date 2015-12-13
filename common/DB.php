<?php
/**
 * Created by PhpStorm.
 * User: zhanat
 * Date: 12/13/15
 * Time: 4:18 PM
 */

namespace common;

use PDO;

class DB
{

    use Singleton;

    /**
     * gets the instance via lazy initialization (created on first usage)
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            /** @var PDO instance */
            static::$instance = new PDO('mysql:host=localhost;dbname=wunderkind', 'root', '');
            static::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return static::$instance;
    }

}