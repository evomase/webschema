<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 13:13
 */

namespace WebSchema\Controllers;

abstract class Controller
{
    protected static $instance;

    protected function __construct()
    {
    }

    public static function boot()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
    }

    public static function shutdown()
    {
        static::$instance = null;
    }
}