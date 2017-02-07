<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 13:28
 */

namespace WebSchema\Models\Traits;

trait HasCollection
{
    /**
     * @var \ArrayObject
     */
    protected static $collection;

    /**
     * Clears the collection array
     */
    public static function clearCollection()
    {
        static::bootCollection();
    }

    /**
     * Initialises the collection array
     */
    public static function bootCollection()
    {
        static::$collection = new \ArrayObject();
    }

    /**
     * @param int|string $id
     * @param object     $model
     */
    protected function put($id, $model)
    {
        if (static::$collection->offsetExists($id)) {
            return;
        }

        static::$collection[$id] = $model;
    }
}