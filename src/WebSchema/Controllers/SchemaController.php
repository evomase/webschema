<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 21/12/2016
 * Time: 16:22
 */

namespace WebSchema\Controllers;

use WebSchema\Factory\PropertyFactory;
use WebSchema\Factory\TypeFactory;
use WebSchema\Utils\Interfaces\Bootable;

class SchemaController implements Bootable
{
    private static $instance;

    private function __construct()
    {
        add_action('wp_ajax_schema_get_all', array($this, 'getAll'));
        add_action('wp_ajax_schema_get_template', array($this, 'getTemplate'));
    }

    public static function boot()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'types'      => ($types = TypeFactory::getAll()),
            'properties' => PropertyFactory::getAll(),
            'tree'       => TypeFactory::createTree($types)
        ]);
        exit;
    }

    public function getTemplate()
    {
        header('Content-Type: text/html');
        include WEB_SCHEMA_DIR . '/resources/templates/dialog.tpl.php';
        exit;
    }
}