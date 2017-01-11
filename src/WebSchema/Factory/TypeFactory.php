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
            print_r(implode(',', $ancestors) . PHP_EOL);

            $tree = self::buildTree($data, $ancestors, $tree);
        }

        return $tree;
    }

    /**
     * @param array $data
     * @param array $ancestors
     * @param array $tree
     * @return array
     */
    public static function buildTree(array $data, array $ancestors, array $tree = [])
    {
        foreach ($ancestors as $index => $ancestor) {
            if (empty($tree[$ancestor])) {
                $tree[$ancestor] = $data[$ancestor];
                $tree[$ancestor]['children'] = [];
            }

            unset($ancestors[$index]);
            $children = self::buildTree($data, $ancestors);

            if ($children) {
                $tree[$ancestor]['children'] = array_replace_recursive($tree[$ancestor]['children'], $children);
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