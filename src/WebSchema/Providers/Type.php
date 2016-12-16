<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:01
 */

namespace WebSchema\Providers;

use WebSchema\Model\Type as TypeModel;


class Type
{
    public static function createOrUpdate(array $types)
    {
        $count = count($types);

        while ($count != 0) {
            if (!$current = each($types)) {
                reset($types);
                $current = each($types);
            }

            $data = $current['value'];
            $id = $current['key'];

            if ($parent = $data['parent']) {
                if ((!$parent = TypeModel::get($parent)) && !empty($types[$data['parent']])) {
                    continue;
                }

                if ($parent) {
                    $data['parent'] = $parent->getID();
                }
            }

            if (!$type = TypeModel::get($id)) {
                $type = new TypeModel($data);
            } else {
                $type->fill($data);
            }

            $type->save();

            unset($types[$id]);
            $count = count($types);
        }

        return $count;
    }

    public static function boot()
    {
        TypeModel::boot();
    }
}