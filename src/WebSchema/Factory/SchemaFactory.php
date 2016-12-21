<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 16:46
 */

namespace WebSchema\Factory;


class SchemaFactory
{
    private static $instance;

    public function __construct()
    {
    }

    public function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}