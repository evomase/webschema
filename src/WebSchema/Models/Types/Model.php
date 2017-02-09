<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 17:12
 */

namespace WebSchema\Models\Types;

use WebSchema\Models\Type;

abstract class Model
{
    /**
     * @var \WebSchema\Models\Type
     */
    protected $schema;
    protected $name;

    public function __construct()
    {
    }

    /**
     * @return \WebSchema\Models\Type
     */
    public function getSchema()
    {
        if (empty($this->schema)) {
            $this->schema = Type::get($this->getTypeName());
        }

        return $this->schema;
    }

    public function getTypeName()
    {
        if (empty($this->name)) {
            $this->name = (new \ReflectionClass($this))->getShortName();
        }

        return $this->name;
    }
}