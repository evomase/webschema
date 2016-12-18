<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Providers;

use WebSchema\Model\Property;
use WebSchema\Model\TypeProperty as Model;
use WebSchema\Model\Type;


class TypeProperty
{
    public static function createOrUpdate(array $data)
    {
        foreach ($data as $type) {
            $properties = $type['properties'];
            $id = $type[Type::FIELD_ID];

            foreach($properties as $property)
            {
                $property = Property::get($property);

                $model = new Model([
                    Model::FIELD_TYPE_ID => $id,
                    Model::FIELD_PROPERTY_ID => $property->getID()
                ]);

                $model->save();
            }
        }
    }

    public static function boot()
    {
        Model::boot();
    }
}