<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 17:12
 */

namespace WebSchema\Models\DataTypes;

use WebSchema\Models\DataTypes\Interfaces\Adapter;
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
     * @var Type
     */
    protected static $typeClass = Type::class;
    /**
     * @var \WP_Post $post
     */
    protected $post;
    protected $required, $data = [];
    /**
     * @var Adapter
     */
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->fill();
    }

    /**
     * @internal param array $data
     */
    protected function fill()
    {
    }

    /**
     * @return Type
     */
    public static function getSchema()
    {
        if (empty(static::$schema)) {
            $class = static::$typeClass;

            static::$schema = $class::get(static::getName());
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
     * @param string $class
     */
    public static function setTypeClass($class)
    {
        if (class_exists($class) && is_subclass_of($class, Type::class)) {
            static::$typeClass = $class;
        }
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
     * @param string $url
     * @return array|null
     */
    public function getImage($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $file = null;

            if (strpos($url, WEB_SCHEMA_BASE_URL) !== false) {
                $file = WEB_SCHEMA_BASE_DIR . str_replace(WEB_SCHEMA_BASE_URL, '', $url);
            }

            if (($image = getimagesize(($file) ?: $url)) !== false) {
                $width = $image[0];

                //requirements as per Google AMP
                if ($width >= WEB_SCHEMA_AMP_IMAGE_MIN_WIDTH &&
                    in_array($image[2], [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG])
                ) {
                    return [
                        'url'    => $url,
                        'width'  => $width,
                        'height' => $image[1]
                    ];
                }
            }
        }

        return null;
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