<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Providers;

use WebSchema\Model\Property as Model;

class Property
{
    public static function createOrUpdate(array $data)
    {
        foreach ($data as $row) {
            if (!$property = Model::get($row[Model::FIELD_ID])) {
                $property = new Model($row);
            } else {
                $property->fill($row);
            }

            $property->save();
        }
    }

    public static function boot()
    {
        Model::boot();
    }
}