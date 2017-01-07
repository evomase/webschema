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
    /**
     * @param array $data
     * @return int
     */
    public static function createOrUpdate(array $data)
    {
        //TODO: refactor this code >_<
        $data = self::setParents($data);
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

    /**
     * @param array $data
     * @return array
     */
    private static function setParents(array $data)
    {
        foreach ($data as $id => $type) {
            if (empty($type['ancestors'])) {
                $data[$id]['parent'] = null;
            } else {
                $data[$id]['parent'] = end($type['ancestors']);
            }
        }

        return $data;
    }

    /**
     * @return Type[]
     */
    public static function getAll()
    {
        $models = Type::getAll();
        $data = [];

        foreach ($models as $id => $model) {
            $data[$id] = $model->toArray();
        }

        return $data;
    }

    public static function boot()
    {
        Type::boot();
    }
}