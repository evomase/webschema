<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 13:13
 */

namespace WebSchema\Controllers;

use WebSchema\Traits\IsSingleton;
use WebSchema\Traits\UsesHooks;

abstract class Controller
{
    use IsSingleton;
    use UsesHooks;

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $hooks;

    public function __construct()
    {
        static::$instance = $this;
    }

    public static function shutdown()
    {
        static::$instance->removeHooks();

        static::$instance = null;
    }
}