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
        return $this->getData();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function fill(array $data)
    {
        $data = array_intersect_key($data, $this->data);
        $this->data = array_merge($this->data, $data);
    }
}