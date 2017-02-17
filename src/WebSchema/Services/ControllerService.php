<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 17:21
 */

namespace WebSchema\Services;

use WebSchema\Controllers\Admin\PostController;
use WebSchema\Controllers\Admin\SettingsController;
use WebSchema\Controllers\AjaxController;

class ControllerService extends Service
{
    public static function boot()
    {
        if (is_admin()) {
            AjaxController::boot();
            SettingsController::boot();
            PostController::boot();
        }
    }

    public static function shutdown()
    {
        if (is_admin()) {
            AjaxController::shutdown();
            SettingsController::shutdown();
            PostController::shutdown();
        }
    }
}