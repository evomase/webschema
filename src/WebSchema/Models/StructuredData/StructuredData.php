<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:56
 */

namespace WebSchema\Models\StructuredData;

use WebSchema\Models\StructuredData\Types\Model as DataType;

class StructuredData
{
    const FILTER_STRUCTURE_DATA_TYPES = 'web-schema-structured-data-types';

    private static $types = [
        'Article'     => 'WebSchema\Models\StructuredData\Types\Article',
        'NewsArticle' => 'WebSchema\Models\StructuredData\Types\NewsArticle',
        'BlogPosting' => 'WebSchema\Models\StructuredData\Types\BlogPosting',

        'VideoObject' => 'WebSchema\Models\StructuredData\Types\VideoObject'
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