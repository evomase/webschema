<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 14:29
 */

namespace WebSchema\Utils\Interfaces;

interface Bootable
{
    public static function boot();
    public static function shutdown();
}