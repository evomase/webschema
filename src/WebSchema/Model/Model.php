<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/12/2016
 * Time: 20:19
 */

namespace WebSchema\Model;


abstract class Model
{
    /**
     * @var \ArrayObject
     */
    protected static $collection;
    protected static $key = 'id';
    protected static $table;

    /**
     * @var \wpdb
     */
    protected static $db;

    protected $data = [];
    protected $new = true;

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->fill($data);
    }

    /**
     * @param array $data
     */
    public function fill(array $data)
    {
        $data = array_intersect_key($data, $this->data);
        $this->data = array_merge($this->data, $data);
    }

    /**
     * @param $id
     * @return static
     */
    public static function get($id)
    {
        if (static::$collection->offsetExists($id)) {
            static::$collection->offsetGet($id);
        }

        if ($data = static::search($id, static::$key)) {
            return current($data);
        }

        return null;
    }

    /**
     * @param        $value
     * @param string $column
     * @return array
     */
    public static function search($value, $column = 'name')
    {
        $data = [];
        $query = static::$db->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . $column . ' = %s', $value);
        $results = static::$db->get_results($query, ARRAY_A);

        foreach ($results as $row) {
            $model = new static($row);
            $model->new = false;

            $model->fill($row);
            $model->put($row[static::$key], $model);

            $data[] = $model;
        }

        return $data;
    }

    /**
     * @param       $id
     * @param Model $model
     */
    protected function put($id, Model $model)
    {
        if (static::$collection->offsetExists($id)) {
            return;
        }

        static::$collection[$id] = $model;
    }

    /**
     *
     */
    public static function clearCollection()
    {
        static::$collection = new \ArrayObject();
    }

    /**
     * @param $value
     * @return Model
     */
    public static function find($value)
    {
        return (new static())->search($value);
    }

    public static function boot()
    {
        global $wpdb;

        static::$collection = new \ArrayObject();
        static::$db = $wpdb;
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
     * @return $this|null
     */
    protected function load($id)
    {
        return $this->search($id, static::$key);
    }
}