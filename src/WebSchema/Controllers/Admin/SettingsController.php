<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 13:12
 */

namespace WebSchema\Controllers\Admin;

use WebSchema\Controllers\Controller;

class SettingsController extends Controller
{
    const SLUG = 'web-schema';

    protected static $instance;

    protected function __construct()
    {
        add_action('admin_menu', [$this, 'addMenus']);
    }

    public function addMenus()
    {
        add_options_page('Web Schema Settings', 'Web Schema', 'manage_options', self::SLUG, [$this, 'get']);
    }

    public function get()
    {
        include WEB_SCHEMA_DIR . '/resources/templates/admin/settings.tpl.php';
    }
}