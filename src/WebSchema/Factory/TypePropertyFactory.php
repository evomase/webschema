<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Factory;

use WebSchema\Model\Property;
use WebSchema\Model\Type;
use WebSchema\Model\TypeProperty;


class TypePropertyFactory
{
    /**
     * @param array $data
     */
    public static function createOrUpdate(array $data)
    {
        foreach ($data as $type) {
            $properties = $type['properties'];
            $id = $type[Type::FIELD_ID];

            foreach ($properties as $property) {
                $property = Property::get($property);

                $model = new TypeProperty([
                    TypeProperty::FIELD_TYPE_ID     => $id,
                    TypeProperty::FIELD_PROPERTY_ID => $property->getID()
                ]);

                $model->save();
            }
        }
    }

    public static function boot()
    {
        TypeProperty::boot();
    }
}