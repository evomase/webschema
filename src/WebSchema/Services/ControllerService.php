<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 17:21
 */

namespace WebSchema\Services;

use WebSchema\Controllers\Admin\PostController as AdminPostController;
use WebSchema\Controllers\Admin\SettingsController;
use WebSchema\Controllers\AjaxController;
use WebSchema\Controllers\PostController as FrontPostController;

class ControllerService extends Service
{
    public static function boot()
    {
        if (is_admin()) {
            new AjaxController();
            new SettingsController();
            new AdminPostController();
        }

        new FrontPostController();
    }

    public static function shutdown()
    {
        if (is_admin()) {
            AjaxController::shutdown();
            SettingsController::shutdown();
            AdminPostController::shutdown();
        }

        FrontPostController::shutdown();
    }
}