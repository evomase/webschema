<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/12/2016
 * Time: 20:19
 */

namespace WebSchema\Models;

use WebSchema\Models\Traits\HasCollection;
use WebSchema\Models\Traits\HasData;

abstract class Model
{
    use HasCollection;
    use HasData;

    protected static $key = 'id';
    protected static $table;
    /**
     * @var \wpdb
     */
    protected static $db;

    protected $data = [];
    protected $new = true;

    /**
     * @var \ArrayObject
     */
    protected static $collection;

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * @param $id
     * @return static
     */
    public static function get($id)
    {
        if (static::$collection->offsetExists($id)) {
            return static::$collection->offsetGet($id);
        }

        if ($data = static::search($id, static::$key)) {
            return current($data);
        }

        return null;
    }

    /**
     * @param        $value
     * @param string $column
     * @return Model[]
     */
    public static function search($value, $column = 'name')
    {
        $data = [];
        /** @noinspection SqlResolve */
        $query = static::$db->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . $column . ' = %s', $value);
        $results = static::$db->get_results($query, ARRAY_A);

        foreach ($results as $row) {
            $model = new static($row);
            $model->new = false;

            $model->put($row[static::$key], $model);

            $data[$row[static::$key]] = $model;
        }

        return $data;
    }

    /**
     * @return Model[]
     */
    public static function getAll()
    {
        $data = [];
        $results = static::$db->get_results('SELECT * FROM ' . static::$table, ARRAY_A);

        foreach ($results as $row) {
            $model = new static($row);
            $model->new = false;

            $model->put($row[static::$key], $model);

            $data[$row[static::$key]] = $model;
        }

        return $data;
    }

    /**
     * @param $value
     * @return Model[]
     */
    public static function find($value)
    {
        return static::search($value);
    }

    public static function boot()
    {
        global $wpdb;

        static::$db = $wpdb;
        static::bootCollection();
    }

    public function save()
    {
        if ($this->new) {
            $this->insert();
            $this->new = false;
        } else {
            $this->update();
        }

        if (array_key_exists(static::$key, $this->data)) {
            $this->put($this->data[static::$key], $this);
        }
    }

    /**
     * @return mixed
     */
    abstract protected function insert();

    /**
     * @return mixed
     */
    abstract protected function update();

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->data[static::$key];
    }

    /**
     * @param $id
     * @return Model|null
     */
    protected function load($id)
    {
        return current($this->search($id, static::$key));
    }
}