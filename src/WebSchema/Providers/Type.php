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
            $current = each($types)['value'];
            $data = $current['value'];
            $data['parent_id'] = 0;

            if ($parent = $data['parent']) {
                if ($parent != TypeModel::find($parent) && !empty($types[$parent])) {
                    continue;
                }

                $data['parent_id'] = $parent->getID();
            }

            if (!$type = TypeModel::find($data['name'])) {
                $type = new TypeModel($data);
            } else {
                $type->fill($data);
            }

            $type->save();

            unset($types[$current['key']]);
            $count = count($types);
        }
    }

    public static function boot()
    {
        TypeModel::boot();
    }
}