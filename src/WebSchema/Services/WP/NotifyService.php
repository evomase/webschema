<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 19:26
 */

namespace WebSchema\Services\WP;

use WebSchema\Models\WP\Notify;
use WebSchema\Services\Service;

class NotifyService extends Service
{
    public static function boot()
    {
        Notify::boot();
    }

    public static function shutdown()
    {
    }
}