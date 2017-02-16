<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 17:21
 */

namespace WebSchema\Services;

use WebSchema\Controllers\Admin\SettingsController;
use WebSchema\Controllers\AjaxController;

class ControllerService extends Service
{
    public static function boot()
    {
        AjaxController::boot();
        SettingsController::boot();
    }

    public static function shutdown()
    {
        AjaxController::shutdown();
        SettingsController::shutdown();
    }
}