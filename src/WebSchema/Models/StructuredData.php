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
        'Article' => 'WebSchema\Models\DataTypes\Article'
    ];

    private function __construct()
    {
    }

    /**
     * @param string $dataType
     * @return DataType|null
     */
    public static function get($dataType)
    {
        if (!empty(self::DATA_TYPES[$dataType])) {
            return self::DATA_TYPES[$dataType];
        }

        return null;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return self::DATA_TYPES;
    }
}