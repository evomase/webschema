<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 13:08
 */

namespace WebSchema\Models\WP;

use WebSchema\Models\StructuredData\StructuredData;
use WebSchema\Models\StructuredData\Types\Model as StructuredDataType;
use WebSchema\Models\Traits\HasCollection;
use WebSchema\Models\Traits\HasData;
use WebSchema\Models\WP\Adapters\Model as Adapter;

class Post
{
    use HasCollection;
    use HasData {
        fill as protected;
    }

    const FIELD_DATA_TYPE = 'data-type';
    const FIELD_JSON_LD = 'json-ld';
    const FILTER_POST_ADAPTER = 'web-schema-post-adapter';
    const META_KEY = 'web-schema';
    const POST_TYPE = 'post';

    /**
     * @var \ArrayObject
     */
    protected static $collection;

    protected $data = [
        self::FIELD_DATA_TYPE => null,
        self::FIELD_JSON_LD   => ''
    ];

    protected $id;
    protected $new = false;

    protected function __construct($id)
    {
        $this->id = $id;
    }

    public static function boot()
    {
        add_action('add_meta_boxes', function () {
            static::addMetaBox();
        });

        add_action('save_post', function ($id, \WP_Post $post) {
            if (!static::isValidPostType($post)) {
                return;
            }

            $model = static::get($id);

            if (($data = $_POST[static::META_KEY]) && $model->isValid($post, $data)) {
                $model->fill($data);
                $model->save();
            }
        }, 10, 2);

        static::bootCollection();
    }

    /**
     * @param array $types
     */
    protected static function addMetaBox(array $types = [])
    {
        if (empty($types)) {
            $types = array_merge(Settings::get(Settings::FIELD_POST_TYPES), [static::POST_TYPE]);
        }

        add_meta_box(static::META_KEY, 'Web Schema - Structured Data', function (\WP_Post $post) {
            static::renderMetaBox($post);
        }, $types, 'advanced', 'high');
    }

    protected static function renderMetaBox(\WP_Post $post)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $types = StructuredData::getTypes();

        foreach ($types as $id => $class) {
            /**
             * @var StructuredDataType $class
             */

            $types[$id] = $class::getSchema()->toArray();
        }

        $model = static::get($post->ID);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $data = $model->data;
        $data[self::FIELD_JSON_LD] = $model->getPrettyJson();

        include WEB_SCHEMA_DIR . '/resources/templates/meta-box.tpl.php';
    }

    /**
     * @param $id
     * @return mixed|static
     */
    public static function get($id)
    {
        if (static::$collection->offsetExists($id)) {
            return static::$collection->offsetGet($id);
        }

        /**
         * @var \WP_Post $post
         */
        if (($post = get_post($id)) && static::isValidPostType($post)) {
            $model = new static($id);
            $model->load();

            return $model;
        }

        return null;
    }

    /**
     * @param \WP_Post $post
     * @return bool
     */
    protected static function isValidPostType(\WP_Post $post)
    {
        return (in_array($post->post_type,
            array_merge(Settings::get(Settings::FIELD_POST_TYPES), [static::POST_TYPE])));
    }

    protected function load()
    {
        $data = get_post_meta($this->id, static::META_KEY, true);

        if (empty($data)) {
            $this->new = true;
        } else {
            $this->fill($data);
        }

        $this->put($this->id, $this);
    }

    /**
     * @return string|null
     */
    public function getPrettyJson()
    {
        //make the json pretty ;-)
        return ($this->data[self::FIELD_JSON_LD]) ? json_encode(json_decode($this->getJson(), true),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return $this->data[self::FIELD_JSON_LD];
    }

    /**
     * @param \WP_Post $post
     * @param array    $data
     * @return bool
     */
    protected function isValid(\WP_Post $post, array $data)
    {
        if (empty($data)) {
            return false;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }

        if (!current_user_can('edit_' . $post->post_type, $this->id)) {
            return false;
        }

        return true;
    }

    public function save()
    {
        $this->generateJson();

        if ($this->new) {
            add_post_meta($this->id, static::META_KEY, $this->data, true);

            $this->new = false;
        } else {
            update_post_meta($this->id, static::META_KEY, $this->data);
        }
    }

    /**
     * @throws \TypeError
     */
    protected function generateJson()
    {
        if ($this->data[self::FIELD_DATA_TYPE] && ($class = StructuredData::get($this->data[self::FIELD_DATA_TYPE]))) {
            $adapter = 'WebSchema\Models\WP\Adapters\\' . $this->data[self::FIELD_DATA_TYPE];
            $adapter = apply_filters(self::FILTER_POST_ADAPTER, $adapter);

            if (class_exists($adapter) && class_exists($class)) {
                /**
                 * @var Adapter $adapter
                 */

                $adapter = new $adapter(get_post($this->id));

                /**
                 * @var StructuredDataType $type
                 */
                $type = new $class($adapter);

                if (!($type instanceof StructuredDataType)) {
                    throw new \TypeError('The type class ' . get_class($type) . ' must extend ' . StructuredDataType::class);
                }

                try {
                    if ($json = $type->generateJSON()) {
                        $this->data[self::FIELD_JSON_LD] = $json;
                    }
                } catch (\UnexpectedValueException $e) {
                    Notify::add($e->getMessage(), Notify::NOTICE_ERROR);

                    $this->data[self::FIELD_JSON_LD] = null;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getJsonScript()
    {
        $json = $this->getJson();

        return (!$json) ? null : '<script type="application/ld+json">' . $json . '</script>';
    }
}