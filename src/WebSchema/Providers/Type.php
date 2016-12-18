<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Providers;

use WebSchema\Model\Type as Model;


class Type
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

            if ($parent = $row[Model::FIELD_PARENT]) {
                if ((!$parent = Model::get($parent)) && !empty($types[$row[Model::FIELD_PARENT]])) {
                    continue;
                }

                if ($parent) {
                    $row[Model::FIELD_PARENT] = $parent->getID();
                }
            }

            if (!$type = Model::get($id)) {
                $type = new Model($row);
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
        Model::boot();
    }
}