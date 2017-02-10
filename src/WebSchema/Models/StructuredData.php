<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:56
 */

namespace WebSchema\Models;

use WebSchema\Models\DataTypes\Model as DataType;

class StructuredData
{
    const DATA_TYPES = [
        'WebSchema\Models\DataTypes\Article'
    ];

    /**
     * @var DataType[]
     */
    private static $dataTypes = [];

    private function __construct()
    {
    }

    /**
     * @param string $dataType
     * @return DataType|null
     */
    public static function get($dataType)
    {
        if (empty(self::$dataTypes)) {
            self::getTypes();
        }

        if (!empty(self::$dataTypes[$dataType])) {
            return self::$dataTypes[$dataType];
        }

        return null;
    }

    /**
     * @return DataType[]
     */
    public static function getTypes()
    {
        if (empty(self::$dataTypes)) {
            foreach (self::DATA_TYPES as $class) {
                /**
                 * @var DataType $dataType
                 */
                $dataType = new $class();
                self::$dataTypes[$dataType->getName()] = $dataType;
            }

        }

        return self::$dataTypes;
    }
}