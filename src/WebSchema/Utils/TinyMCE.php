<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 16:48
 */

namespace WebSchema\Utils;


class TinyMCE
{
    public static function boot()
    {
        add_filter('mce_external_plugins', array(__CLASS__, 'register'));
        add_filter('mce_buttons', array(__CLASS__, 'addButtons'));
    }

    public static function register(array $plugins)
    {
        $plugins['webschema'] = plugins_url('webschema') . '/resources/js/tinymce/webschema.js';

        return $plugins;
    }

    public static function addButtons(array $buttons)
    {
        return array_merge($buttons, array('webschema'));
    }
}