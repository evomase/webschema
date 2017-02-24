<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 14:48
 */

namespace WebSchema\Models\StructuredData\Types;

use WebSchema\Models\Type;

class NewsArticle extends Article
{
    /**
     * @var Type $schema
     */
    protected static $schema;
    protected static $name;
}