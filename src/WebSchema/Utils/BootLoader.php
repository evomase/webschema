<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 14:11
 */

namespace WebSchema\Utils;

use WebSchema\Services\AMP\RouteService;
use WebSchema\Services\ControllerService;
use WebSchema\Services\SchemaService;
use WebSchema\Services\StructuredDataService;
use WebSchema\Services\WP\NotifyService;
use WebSchema\Services\WP\PostService;
use WebSchema\Services\WP\SettingsService;
use WebSchema\Utils\Interfaces\Bootable;

class BootLoader
{
    const CLASSES = [
        SchemaService::class         => null,
        ControllerService::class     => null,
        StructuredDataService::class => null,

        //WP Services
        PostService::class           => null,
        SettingsService::class       => null,
        NotifyService::class         => 'is_admin',

        Installer::class    => 'is_admin',
        TinyMCE::class      => 'is_admin',

        //AMP
        RouteService::class => null
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
            self::$instance->boot();
        }
    }

    /**
     * @throws \BadFunctionCallException
     */
    private function boot()
    {
        $this->call('boot');
        self::$instance->booted = true;
    }

    /**
     * @param string $method
     */
    private function call($method)
    {
        foreach (self::CLASSES as $class => $func) {
            if (!class_exists($class) || !in_array(Bootable::class, class_implements($class))) {
                throw new \BadFunctionCallException('Web Schema BootLoader: The class ' . $class . ' does not exist or' .
                    ' not implementing the Bootable interface');
            }

            if ($func && !call_user_func($func)) {
                continue;
            }

            call_user_func($class . '::' . $method);
        }
    }

    public static function stop()
    {
        if (self::$instance->booted) {
            self::$instance->shutdown();
        }
    }

    private function shutdown()
    {
        $this->call('shutdown');
        self::$instance->booted = false;
    }
}