<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Providers;

use WebSchema\Model\Property as PropertyModel;

class Property
{
    public static function createOrUpdate(array $properties)
    {
        foreach ($properties as $data) {
            if (!$property = PropertyModel::find($data['name'])) {
                $property = new PropertyModel($data);
            } else {
                $property->fill($data);
            }

            $property->save();
        }
    }

    public static function boot()
    {
        PropertyModel::boot();
    }
}