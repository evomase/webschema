<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 13:08
 */

namespace WebSchema\Models\WP;

use WebSchema\Models\StructuredData;
use WebSchema\Models\Traits\HasCollection;
use WebSchema\Models\Traits\HasData;
use WebSchema\Models\Types\Model;

class Post
{
    use HasCollection;
    use HasData;

    const FIELD_DATA_TYPE = 'data-type';
    const FIELD_MICRO_DATA = '';

    const META_KEY = 'web-schema';
    const POST_TYPE = 'post';

    protected $data = [
        self::FIELD_DATA_TYPE  => null,
        self::FIELD_MICRO_DATA => ''
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

        foreach ($types as $id => $type) {
            /**
             * @var Model $type
             */
            $types[$id] = $type->getSchema()->toArray();
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
        $data = get_post_meta($this->id, static::META_KEY);

        if (empty($data)) {
            $this->new = true;
        } else {
            $this->fill($data);
        }

        $this->put($this->id, $this);
    }

    /**
     * @param array $data
     */
    public function fill(array $data)
    {
        $data = array_intersect_key($data, $this->data);
        $this->data = array_merge($this->data, $data);
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
        if ($this->new) {
            add_post_meta($this->id, static::META_KEY, $this->data, true);

            $this->new = false;
        } else {
            update_post_meta($this->id, static::META_KEY, $this->data);
        }
    }

    public function getMicroData()
    {

    }

    public function generateMicroData()
    {

    }
}