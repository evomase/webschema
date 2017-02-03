<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:55
 */

namespace tests\WebSchema;

use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public static function tearDownAfterClass()
    {
        global $wpdb;

        /**
         * @var $wpdb \wpdb
         */
        $wpdb->query('DROP TABLE IF EXISTS ' . WEB_SCHEMA_TABLE_TYPE_PROPERTIES);
        $wpdb->query('DROP TABLE IF EXISTS ' . WEB_SCHEMA_TABLE_TYPES);
        $wpdb->query('DROP TABLE IF EXISTS ' . WEB_SCHEMA_TABLE_PROPERTIES);

        Type::clearCollection();
        Property::clearCollection();
        TypeProperty::clearCollection();
    }
}