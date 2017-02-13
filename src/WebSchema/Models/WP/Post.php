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

    private $id;
    private $new = false;

    private function __construct($id)
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

        $model = new static($id);
        $model->load();

        return $model;
    }

    private function load()
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

        add_action('save_post', function ($id) {
            $model = static::get($id);

            if (($data = $_POST[static::META_KEY]) && $model->isValid($data)) {
                $model->fill($data);
                $model->save();
            }
        });

        static::bootCollection();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data)
    {
        if (empty($data)) {
            return false;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }

        if (!current_user_can('edit_' . static::POST_TYPE, $this->id)) {
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

    private function generateJson()
    {
        if ($this->data[self::FIELD_DATA_TYPE] && ($class = StructuredData::get($this->data[self::FIELD_DATA_TYPE]))) {
            /**
             * @var DataType $type
             */
            $type = new $class(get_post($this->id));

            if ($json = $type->generateJSON()) {
                $this->data[self::FIELD_JSON_LD] = $json;
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