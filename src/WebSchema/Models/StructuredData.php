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
    const FILTER_STRUCTURE_DATA_TYPES = 'web-schema-structured-data-types';

    private static $types = [
        'Article'     => 'WebSchema\Models\DataTypes\Article',
        'NewsArticle' => 'WebSchema\Models\DataTypes\NewsArticle',
        'BlogPosting' => 'WebSchema\Models\DataTypes\BlogPosting',

        'VideoObject' => 'WebSchema\Models\DataTypes\VideoObject'
    ];

    private function __construct()
    {
    }

    public static function boot()
    {
        self::$types = apply_filters(self::FILTER_STRUCTURE_DATA_TYPES, self::$types);
    }

    /**
     * @param string $type
     * @return DataType|null
     */
    public static function get($type)
    {
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
        return self::$types;
    }
}