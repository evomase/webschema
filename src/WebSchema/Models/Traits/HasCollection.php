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
     * @return array
     */
    public static function getCollection()
    {
        /**
         * @var \ArrayObject $collection
         */

        $collection = static::$collection;

        return $collection->getArrayCopy();
    }

    /**
     * @param int|string $id
     * @param object     $model
     */
    protected function put($id, $model)
    {
        /**
         * @var \ArrayObject $collection
         */
        $collection = static::$collection;

        if ($collection->offsetExists($id)) {
            return;
        }

        $collection[$id] = $model;
    }
}