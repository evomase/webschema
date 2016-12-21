<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 21/12/2016
 * Time: 16:22
 */

namespace WebSchema\Controller;

class SchemaController
{
    private static $instance;

    private function __construct()
    {
        add_action('wp_ajax_schema_get_all', array($this, 'getAll'));
    }

    public static function boot()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
    }

    public function getAll()
    {

    }
}