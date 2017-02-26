<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 23/02/2017
 * Time: 22:43
 */

namespace WebSchema\Services\AMP;

use WebSchema\Models\AMP\Route;
use WebSchema\Services\Service;

class RouteService extends Service
{
    public static function boot()
    {
        Route::boot();
    }

    public static function shutdown()
    {
        // TODO: Implement shutdown() method.
    }
}