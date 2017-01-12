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
     */
    public static function createOrUpdate(array $data)
    {
        foreach ($data as $row) {
            if (!$model = Type::get($row[Type::FIELD_ID])) {
                $model = new Type($row);
            } else {
                $model->fill($row);
            }

            $model->save();
        }
    }

    /**
     * @return Type[]
     */
    public static function getAll()
    {
        $models = Type::getAll();
        $data = [];

        ksort($models);

        foreach ($models as $id => $model) {
            $data[$id] = $model->toArray();
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function createTree(array $data)
    {
        $tree = [];

        foreach ($data as $id => $type) {
            $ancestors = array_merge($type[Type::FIELD_ANCESTORS], [$type[Type::FIELD_ID]]);
            $tree = self::buildTree($data, $ancestors, $tree);
        }

        return $tree;
    }

    /**
     * @param array $data
     * @param array $items
     * @param array $tree
     * @return array
     */
    private static function buildTree(array $data, array $items, array $tree = [])
    {
        foreach ($items as $index => $item) {
            if (empty($tree[$item])) {
                $tree[$item] = $data[$item];
                $tree[$item]['children'] = [];
            }

            unset($items[$index]);

            if ($children = self::buildTree($data, $items)) {
                $tree[$item]['children'] = array_replace_recursive($tree[$item]['children'], $children);
            }

            break;
        }

        return $tree;
    }

    public static function boot()
    {
        Type::boot();
    }
}