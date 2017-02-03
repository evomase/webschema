<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 14:11
 */

namespace WebSchema\Utils;

use WebSchema\Utils\Interfaces\Bootable;

class BootLoader
{
    const CLASSES = [
        '\WebSchema\Factory\PropertyFactory'     => null,
        '\WebSchema\Factory\TypeFactory'         => null,
        '\WebSchema\Factory\TypePropertyFactory' => null,

        '\WebSchema\Utils\Installer' => null,
        '\WebSchema\Utils\TinyMCE'   => 'is_admin',

        '\WebSchema\Controllers\SchemaController' => null,
    ];

    /**
     * @var BootLoader
     */
    private static $instance;

    private $booted = false;

    private function __construct()
    {
    }

    public static function run()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        if (!self::$instance->booted) {
            self::$instance->bootClasses();
        }
    }

    /**
     * @throws \BadFunctionCallException
     */
    private function bootClasses()
    {
        foreach (self::CLASSES as $class => $func) {
            if (!class_exists($class) || !in_array(Bootable::class, class_implements($class))) {
                throw new \BadFunctionCallException('Web Schema BootLoader: The class ' . $class . ' does not exist or' .
                    ' not implementing the Bootable interface');
            }

            if ($func && !call_user_func($func)) {
                continue;
            }

            call_user_func($class . '::boot');
        }

        self::$instance->booted = true;
    }
}