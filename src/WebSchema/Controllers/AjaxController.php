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

class AjaxController extends Controller
{
    protected static $instance;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $this->addAction('wp_ajax_schema_get_all', array($this, 'getAll'));
        $this->addAction('wp_ajax_schema_get_template', array($this, 'getTemplate'));
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAll()
    {
        header('Content-Type: application/json');

        echo json_encode([
            'types'      => ($types = TypeFactory::getAll()),
            'properties' => PropertyFactory::getAll(),
            'tree'       => TypeFactory::createTree($types)
        ]);

        wp_die();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplate()
    {
        header('Content-Type: text/html');
        include WEB_SCHEMA_DIR . '/resources/templates/dialog.tpl.php';

        wp_die();
    }
}