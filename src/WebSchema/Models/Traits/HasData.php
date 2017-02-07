<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 07/02/2017
 * Time: 18:16
 */

namespace WebSchema\Models\Traits;

trait HasData
{
    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        foreach ($this->data as $id => $value) {
            $data[$id] = $value;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}