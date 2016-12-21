<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Factory;

use WebSchema\Model\Property;

class PropertyFactory
{
    public static function createOrUpdate(array $data)
    {
        foreach ($data as $row) {
            if (!$property = Property::get($row[Property::FIELD_ID])) {
                $property = new Property($row);
            } else {
                $property->fill($row);
            }

            $property->save();
        }
    }

    public static function boot()
    {
        Property::boot();
    }
}