<?php
/**
 * Created by PhpStorm.
 * User: zhanat
 * Date: 12/13/15
 * Time: 4:56 PM
 */

namespace common;

interface MovingFilesInterface
{

    public function publish();

    public function subscribe();

    public function getFiles();

}