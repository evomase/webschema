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
     * @var Type $schema
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
    protected $required = [], $data = [];
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
     * @param string $class
     */
    public static function setTypeClass($class)
    {
        if (class_exists($class) && ($class == Type::class || is_subclass_of($class, Type::class))) {
            self::$typeClass = $class;

            static::$schema = null;
        }
    }

    /**
     * @return Type
     */
    public static function getSchema()
    {
        if (empty(static::$schema)) {
            $class = self::$typeClass;

            static::$schema = $class::get(static::getName());
        }

        return static::$schema;
    }

    /**
     * @return string
     */
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
                $field = ($property = Property::get($field)) ? $property->getData()[Property::FIELD_LABEL] : $field;

                throw new \UnexpectedValueException('The Web Schema field "' . $field . '" is required or invalid to' .
                    ' generate a JSON-LD. Please refer to the' .
                    ' <a href="https://developers.google.com/search/docs/data-types/data-type-selector" target="_blank">' .
                    'Structured Data Types API</a> for more information.');
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
        if (($image = $this->getImageSize($url)) && static::isImageValid($image)) {
            return [
                'url'    => $url,
                'width'  => $image[0],
                'height' => $image[1]
            ];
        }

        return null;
    }

    /**
     * @param string $url
     * @return array|null
     */
    protected function getImageSize($url)
    {
        $image = null;

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $file = null;

            if (strpos($url, WEB_SCHEMA_BASE_URL) !== false) {
                $file = WEB_SCHEMA_BASE_DIR . str_replace(WEB_SCHEMA_BASE_URL, '', $url);
            }

            $image = getimagesize(($file) ?: $url);
        }

        return $image;
    }

    /**
     * Requirements as per Google AMP
     *
     * @param array $image (from getimagesize())
     * @return bool
     */
    public static function isImageValid(array $image)
    {
        return ($image[0] >= WEB_SCHEMA_AMP_IMAGE_MIN_WIDTH &&
            in_array($image[2], [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG]));
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