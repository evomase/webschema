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
    private $type, $property, $data;
    private $children = [];

    public function __construct($property, $type, $data = null)
    {
        $this->property = $property;
        $this->type = $type;
        $this->data = $data;
    }

    public function add(Node $node)
    {
        $this->children[] = $node;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getProperty()
    {
        return $this->property;
    }
}