<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 13:59
 */

namespace WebSchema\Models\DataTypes\Traits;

trait HasAuthor
{
    /**
     * @param string $name
     * @return $this
     */
    protected function setAuthor($name)
    {
        return $this->setValue(self::FIELD_AUTHOR, $name);
    }
}