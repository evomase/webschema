<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/02/2017
 * Time: 13:08
 */

namespace WebSchema\Model\WP;

use WebSchema\Models\Traits\HasCollection;

class Post
{
    use HasCollection;

    const META_KEY = 'web-schema';

    private $id;
    private $data;

    private function __construct($id)
    {
        $this->id = $id;
    }

    public static function get($id)
    {
        if (static::$collection->offsetExists($id)) {
            return static::$collection->offsetGet($id);
        }

        $model = new static($id);
        $model->load();

        return $model;
    }

    public function load()
    {
        $this->data = get_post_meta($this->id, static::META_KEY);

        $this->put($this->id, $this);
    }

    public function save()
    {
        update_post_meta($this->id, static::META_KEY, $this->data);
    }
}