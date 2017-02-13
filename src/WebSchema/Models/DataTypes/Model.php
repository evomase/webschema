<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 17:12
 */

namespace WebSchema\Models\DataTypes;

use WebSchema\Models\Property;
use WebSchema\Models\Traits\HasData;
use WebSchema\Models\Type;
use WebSchema\Utils\JsonLD;

abstract class Model
{
    use HasData {
        fill as protected;
    }

    /**
     * @var \WebSchema\Models\Type $schema
     */
    protected static $schema;
    protected static $name;

    /**
     * @var \WP_Post $post
     */
    protected $post;
    protected $required, $data = [];

    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->fill();
    }

    /**
     * @internal param array $data
     */
    protected function fill()
    {
    }

    /**
     * @return \WebSchema\Models\Type
     */
    public static function getSchema()
    {
        if (empty(static::$schema)) {
            static::$schema = Type::get(static::getName());
        }

        return static::$schema;
    }

    public static function getName()
    {
        if (empty(static::$name)) {
            static::$name = (new \ReflectionClass(static::class))->getShortName();
        }

        return static::$name;
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

        return JsonLD::create(new JsonLD\Node($this->getName(), $this->data));
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    protected function setValue($key, $value)
    {
        if ($value && $key && ($property = Property::get($key))
            && ($type = Type::get(current($property->getData()[Property::FIELD_RANGES])))
        ) {
            $this->data[$key] = new JsonLD\Node($type->getData()[Type::FIELD_ID], $value);
        }

        return $this;
    }
}