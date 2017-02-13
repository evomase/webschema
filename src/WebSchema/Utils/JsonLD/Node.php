<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 10/02/2017
 * Time: 17:14
 */

namespace WebSchema\Utils\JsonLD;

class Node
{
    private $type, $data;

    public function __construct($type = null, $data = null)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function add(Node $node)
    {
        if (!is_array($this->data)) {
            $this->data = [];
        }

        $this->data[] = $node;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)json_encode($this->toArray());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        if ($this->data) {
            if ($this->type) {
                $data = ['@type' => $this->type];
            }

            switch (gettype($this->data)) {
                case 'array':
                    foreach ($this->data as $property => $item) {
                        if (is_array($item)) {
                            foreach ($item as $child) {
                                $data[$property][] = $child->toArray();
                            }

                            continue;
                        }

                        if ($item instanceof self) {
                            $data[$property] = $item->toArray();
                            continue;
                        }

                        $data[$property] = $item;
                    }
                    break;

                case 'object':
                    if ($this->data instanceof self) {
                        $data = $this->data->toArray();
                    }
                    break;

                case 'string':
                case 'integer':
                case 'double':
                case 'float':
                    $data = $this->data;
                    break;
            }
        }

        return $data;
    }
}