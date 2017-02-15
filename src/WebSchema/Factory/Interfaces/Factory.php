<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 14:37
 */

namespace WebSchema\Factory\Interfaces;

interface Factory
{
    public static function createOrUpdate(array $data);
}