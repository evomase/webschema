<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 13:36
 */

namespace WebSchema\Services\WP;

use WebSchema\Models\WP\Settings;
use WebSchema\Services\Service;

class SettingsService extends Service
{
    public static function boot()
    {
        Settings::boot();
    }

    public static function shutdown()
    {
    }
}