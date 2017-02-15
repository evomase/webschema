<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 16:48
 */

namespace WebSchema\Utils;


use WebSchema\Utils\Interfaces\Bootable;

class TinyMCE implements Bootable
{
    public static function boot()
    {
        add_filter('mce_external_plugins', array(__CLASS__, 'register'));
        add_filter('mce_buttons', array(__CLASS__, 'addButtons'));

        add_action('tiny_mce_before_init', array(__CLASS__, 'extendConfig'));
        add_action('init', array(__CLASS__, 'addValidAttributes'));
    }

    public static function shutdown()
    {
    }

    public static function register(array $plugins)
    {
        $plugins['webschema'] = plugins_url('webschema') . '/resources/js/tinymce/webschema.js';

        return $plugins;
    }

    public static function extendConfig(array $data)
    {
        $data['wpautop'] = false;

        return $data;
    }

    public static function addValidAttributes()
    {
        global $allowedposttags;

        $allowedposttags['meta'] = [];
    }

    public static function addButtons(array $buttons)
    {
        return array_merge($buttons, array('webschema'));
    }
}