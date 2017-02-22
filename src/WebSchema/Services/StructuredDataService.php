<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 15:56
 */

namespace WebSchema\Services;

use WebSchema\Models\StructuredData;

class StructuredDataService extends Service
{
    public static function boot()
    {
        StructuredData::boot();
    }

    public static function shutdown()
    {
    }
}