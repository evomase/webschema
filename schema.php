<?php
/*
Plugin Name: Web Schema
Plugin URI: https://github.com/evomase/webschema
Description: Web Schema is an open source tool created to make it easier for content creators to markup their content with a collection of schemas
Version: 1.0
Author: David Ogilo
Author URI: http://www.davidogilo.co.uk
License: GPL2
*/

define('WEB_SCHEMA_VERSION', '1.0.0');

require 'config.php';
require 'autoload.php';

\WebSchema\Factory\PropertyFactory::boot();
\WebSchema\Factory\TypeFactory::boot();
\WebSchema\Factory\TypePropertyFactory::boot();
\WebSchema\Utils\Installer::boot();

\WebSchema\Controller\SchemaController::boot();

if (is_admin()) {
    \WebSchema\Utils\TinyMCE::boot();
}