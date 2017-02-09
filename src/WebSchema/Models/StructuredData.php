<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:56
 */

namespace WebSchema\Models;

use WebSchema\Models\Types\Model;

class StructuredData
{
    const CLASS_TYPES = [
        'WebSchema\Models\Types\Article'
    ];

    private static $types = [];

    private function __construct()
    {
    }

    /**
     * @param $type
     * @return Model|null
     */
    public static function get($type)
    {
        if (empty(self::$types)) {
            self::getTypes();
        }

        if (!empty(self::$types[$type])) {
            return self::$types[$type];
        }

        return null;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        if (empty(self::$types)) {
            foreach (self::CLASS_TYPES as $class) {
                /**
                 * @var Model $type
                 */
                $type = new $class();
                self::$types[$type->getTypeName()] = $type;
            }

        }

        return self::$types;
    }
}