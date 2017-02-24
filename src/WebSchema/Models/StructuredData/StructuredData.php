<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:56
 */

namespace WebSchema\Models\StructuredData;

use WebSchema\Models\StructuredData\Types\Article;
use WebSchema\Models\StructuredData\Types\BlogPosting;
use WebSchema\Models\StructuredData\Types\Model as StructuredDataType;
use WebSchema\Models\StructuredData\Types\NewsArticle;
use WebSchema\Models\StructuredData\Types\VideoObject;

class StructuredData
{
    const FILTER_STRUCTURE_DATA_TYPES = 'web-schema-structured-data-types';

    private static $types = [
        'Article'     => Article::class,
        'NewsArticle' => NewsArticle::class,
        'BlogPosting' => BlogPosting::class,

        'VideoObject' => VideoObject::class
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
     * @return StructuredDataType|null
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