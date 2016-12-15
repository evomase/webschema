<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 16:53
 */

global $wpdb;

define('WEB_SCHEMA_DIR', dirname(__FILE__));

//db settings
define('WEB_SCHEMA_TABLE_TYPES', $wpdb->prefix . 'web_schema_types');
define('WEB_SCHEMA_TABLE_PROPERTIES', $wpdb->prefix . 'web_schema_properties');
define('WEB_SCHEMA_TABLE_SCHEMA', $wpdb->prefix . 'web_schema');