<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 16:53
 */

global $wpdb;

define('WEB_SCHEMA_VERSION', '1.1');
define('WEB_SCHEMA_DIR', dirname(__FILE__));
define('WEB_SCHEMA_DIR_URL', plugins_url('webschema'));
define('WEB_SCHEMA_BASE_URL', get_option('siteurl'));
define('WEB_SCHEMA_BASE_DIR', ABSPATH);

//db settings
define('WEB_SCHEMA_TABLE_TYPES', $wpdb->prefix . 'web_schema_types');
define('WEB_SCHEMA_TABLE_PROPERTIES', $wpdb->prefix . 'web_schema_properties');
define('WEB_SCHEMA_TABLE_TYPE_PROPERTIES', $wpdb->prefix . 'web_schema_type_properties');

//AMP settings
define('WEB_SCHEMA_AMP_IMAGE_MIN_WIDTH', 696);

define('WEB_SCHEMA_AMP_PUBLISHER_LOGO_WIDTH', 600);
define('WEB_SCHEMA_AMP_PUBLISHER_LOGO_HEIGHT', 60);

define('WEB_SCHEMA_AMP_JS_FRAMEWORK', 'https://cdn.ampproject.org/v0');
define('WEB_SCHEMA_AMP_VIEWPORT', 'width=device-width,minimum-scale=1,initial-scale=1');
define('WEB_SCHEMA_AMP_STYLE',
    '<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>');
define('WEB_SCHEMA_AMP_STYLE_NO_SCRIPT',
    '<noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>');

define('WEB_SCHEMA_AMP_STYLESHEET_EXPIRY', HOUR_IN_SECONDS);