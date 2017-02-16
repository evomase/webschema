<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 13:08
 */

namespace WebSchema\Models\WP;

use WebSchema\Models\DataTypes\Model as DataType;
use WebSchema\Models\StructuredData;
use WebSchema\Models\Traits\HasCollection;
use WebSchema\Models\Traits\HasData;
use WebSchema\Models\WP\Adapters\Model as Adapter;

class Post
{
    use HasCollection;
    use HasData;

    const FIELD_DATA_TYPE = 'data-type';
    const FIELD_JSON_LD = 'json-ld';

    const META_KEY = 'web-schema';
    const POST_TYPE = 'post';

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

    public static function renderMetaBox(\WP_Post $post)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $types = StructuredData::getTypes();

        foreach ($types as $id => $class) {
            /**
             * @var DataType $class
             */

            $types[$id] = $class::getSchema()->toArray();
        }

        $model = static::get($post->ID);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $data = $model->data;

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
        return ($post->post_type == static::POST_TYPE);
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

    public static function addMetaBox()
    {
        add_meta_box(static::META_KEY, 'Web Schema - Structured Data', array(static::class, 'renderMetaBox'),
            static::POST_TYPE,
            'advanced', 'high');
    }

    public static function boot()
    {
        add_action('add_meta_boxes', array(static::class, 'addMetaBox'));

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

    protected function generateJson()
    {
        if ($this->data[self::FIELD_DATA_TYPE] && ($class = StructuredData::get($this->data[self::FIELD_DATA_TYPE]))) {
            $adapter = 'WebSchema\Models\WP\Adapters\\' . $this->data[self::FIELD_DATA_TYPE];

            if (class_exists($adapter)) {
                /**
                 * @var Adapter $adapter
                 */

                $adapter = new $adapter(get_post($this->id));

                /**
                 * @var DataType $type
                 */
                $type = new $class($adapter);

                try {
                    if ($json = $type->generateJSON()) {
                        $this->data[self::FIELD_JSON_LD] = $json;
                    }
                } catch (\UnexpectedValueException $e) {
                    Notify::notify($e->getMessage(), Notify::NOTICE_ERROR);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return $this->data[self::FIELD_JSON_LD];
    }
}