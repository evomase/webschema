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
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty;

class TypePropertyFactory implements Factory
{
    /**
     * @param array $data
     */
    public static function createOrUpdate(array $data)
    {
        foreach ($data as $type) {
            $properties = $type['properties'];
            $type = $type[Type::FIELD_ID];

            if (!Type::get($type)) {
                continue;
            }

            foreach ($properties as $property) {
                if (!Property::get($property)) {
                    continue;
                }

                $row = [
                    TypeProperty::FIELD_PROPERTY_ID => $property,
                    TypeProperty::FIELD_TYPE_ID     => $type
                ];

                if (!$model = TypeProperty::lookup($type, $property)) {
                    $model = new TypeProperty($row);
                }

                $model->save();
            }
        }
    }
}