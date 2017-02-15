<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Factory;

use WebSchema\Factory\Interfaces\Factory;
use WebSchema\Models\Property;

class PropertyFactory implements Factory
{
    /**
     * @param array $data
     */
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

    /**
     * @return Property[]
     */
    public static function getAll()
    {
        $models = Property::getAll();
        $data = [];

        foreach ($models as $id => $model) {
            $data[$id] = $model->toArray();
        }

        return $data;
    }
}