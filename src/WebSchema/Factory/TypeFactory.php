<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Factory;

use WebSchema\Model\Type;


class TypeFactory
{
    public static function createOrUpdate(array $data)
    {
        //TODO: refactor this code >_<
        $count = count($data);

        while ($count != 0) {
            if (!$current = each($data)) {
                reset($data);
                $current = each($data);
            }

            $row = $current['value'];
            $id = $current['key'];

            if ($parent = $row[Type::FIELD_PARENT]) {
                if ((!$parent = Type::get($parent)) && !empty($types[$row[Type::FIELD_PARENT]])) {
                    continue;
                }

                if ($parent) {
                    $row[Type::FIELD_PARENT] = $parent->getID();
                }
            }

            if (!$type = Type::get($id)) {
                $type = new Type($row);
            } else {
                $type->fill($row);
            }

            $type->save();

            unset($data[$id]);
            $count = count($data);
        }

        return $count;
    }

    public static function boot()
    {
        Type::boot();
    }
}