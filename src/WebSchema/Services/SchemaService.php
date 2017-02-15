<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 17:17
 */

namespace WebSchema\Services;

use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty;

class SchemaService extends Service
{
    public static function boot()
    {
        Type::boot();
        Property::boot();
        TypeProperty::boot();
    }

    public static function shutdown()
    {
        Type::clearCollection();
        Property::clearCollection();
        TypeProperty::clearCollection();
    }
}