<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 17:12
 */

namespace WebSchema\Models\DataTypes;

use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Utils\JsonLD;

abstract class Model
{
    /**
     * @var \WebSchema\Models\Type
     */
    protected $schema, $name;
    protected $required, $data = [];

    public function __construct()
    {
    }

    /**
     * @return \WebSchema\Models\Type
     */
    public function getSchema()
    {
        if (empty($this->schema)) {
            $this->schema = Type::get($this->getName());
        }

        return $this->schema;
    }

    public function getName()
    {
        if (empty($this->name)) {
            $this->name = (new \ReflectionClass($this))->getShortName();
        }

        return $this->name;
    }

    /**
     * @return string
     * @throws \UnexpectedValueException
     */
    public function generateJSON()
    {
        foreach ($this->required as $field) {
            if (empty($this->data[$field])) {
                throw new \UnexpectedValueException('The field "' . $field . '" is required to generate a JSON');
            }
        }

        return JsonLD::create($this->getName(), $this->data);
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    protected function setValue($key, $value)
    {
        if ($value && $key && ($property = Property::get($key))
            && ($type = Type::get(current($property[Property::FIELD_RANGES])))
        ) {
            $this->data[$key] = new JsonLD\Node($key, $type[Type::FIELD_ID], $value);
        }

        return $this;
    }
}