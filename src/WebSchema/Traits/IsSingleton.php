<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 12:08
 */

namespace WebSchema\Traits;

trait IsSingleton
{
    public static function getInstance()
    {
        return static::$instance;
    }
}