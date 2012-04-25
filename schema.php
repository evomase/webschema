<?php 
/*
Plugin Name: Web Schema
Plugin URI: http://www.davidogilo.co.uk
Description: Web Schema is an open source tool created to make it easier for webmasters to markup their content with a collection of schemas
Version: 0.9.0b
Author: David Ogilo
Author URI: http://www.davidogilo.co.uk
License: GPL2
*/
define( 'WEB_SCHEMA_PLUGIN_DIR', dirname( __FILE__ ) );

require_once( WEB_SCHEMA_PLUGIN_DIR . '/classes/schema.class.php' );
require_once( WEB_SCHEMA_PLUGIN_DIR . '/classes/schematype.class.php' );
require_once( WEB_SCHEMA_PLUGIN_DIR . '/classes/schemaproperty.class.php' );

Schema::getInstance();
SchemaProperty::getInstance();
SchemaType::getInstance();
?>