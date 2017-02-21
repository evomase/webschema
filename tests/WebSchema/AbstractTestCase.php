<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:55
 */

namespace WebSchema\Tests;

use WebSchema\Models\WP\Settings;
use WebSchema\Utils\BootLoader;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        BootLoader::run();
    }

    public static function tearDownAfterClass()
    {
        BootLoader::stop();

        static::dropSchemaTables();
        Settings::reset();
    }

    protected static function dropSchemaTables()
    {
        global $wpdb;

        /**
         * @var $wpdb \wpdb
         */
        $wpdb->query('DROP TABLE IF EXISTS ' . WEB_SCHEMA_TABLE_TYPE_PROPERTIES);
        $wpdb->query('DROP TABLE IF EXISTS ' . WEB_SCHEMA_TABLE_TYPES);
        $wpdb->query('DROP TABLE IF EXISTS ' . WEB_SCHEMA_TABLE_PROPERTIES);
    }

    protected static function setDefaultSettings()
    {
        update_option(Settings::NAME, [
            Settings::FIELD_PUBLISHER => [
                Settings::FIELD_PUBLISHER_NAME => 'Tester',
                Settings::FIELD_PUBLISHER_LOGO => WEB_SCHEMA_DIR_URL . '/tests/resources/images/ModelTest.jpg'
            ]
        ]);
    }
}