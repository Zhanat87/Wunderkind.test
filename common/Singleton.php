<?php
/**
 * Created by PhpStorm.
 * User: zhanat
 * Date: 12/13/15
 * Time: 4:34 PM
 */

namespace common;

trait Singleton
{

    /**
     * @var Singleton reference to singleton instance
     */
    private static $instance;

    /**
     * is not allowed to call from outside: private!
     *
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized
     *
     * @return void
     */
    private function __wakeup()
    {
    }

}