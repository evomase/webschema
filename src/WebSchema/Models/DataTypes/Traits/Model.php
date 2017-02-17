<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/02/2017
 * Time: 18:09
 */

namespace WebSchema\Models\DataTypes\Traits;

use WebSchema\Models\Type;

trait Model
{
    /**
     * @var Type $schema
     */
    protected static $schema;
    protected static $name;
}