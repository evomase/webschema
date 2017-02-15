<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 17:21
 */

namespace WebSchema\Services;

use WebSchema\Controllers\SchemaController;

class ControllerService extends Service
{
    public static function boot()
    {
        SchemaController::boot();
    }

    public static function shutdown()
    {
        SchemaController::shutdown();
    }
}