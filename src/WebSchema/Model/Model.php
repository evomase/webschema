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
    protected $table;
    protected $key = 'id';

    /**
     * @var wpdb
     */
    protected $db;

    protected $data = [];

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        global $wpdb;
        $this->db = $wpdb;

        $this->fill($data);
    }

    /**
     * @param array $data
     * @return void
     */
    abstract public function fill(array $data);

    /**
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        if (static::$collection->offsetExists($id)) {
            static::$collection->offsetGet($id);
        }

        return (new static())->load($id);
    }

    /**
     * @param $id
     * @return $this|null
     */
    protected function load($id)
    {
        $query = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $this->key . ' = %d', $id);

        if ($data = $this->db->get_row($query, ARRAY_A)) {
            $this->fill($data);
            $this->put($data[$this->key], $this);
            return $this;
        }

        return null;
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
     * @param $value
     * @return Model
     */
    public static function find($value)
    {
        return (new static())->search($value);
    }

    /**
     * @param        $value
     * @param string $column
     * @return $this|null
     */
    protected function search($value, $column = 'name')
    {
        $query = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $column . ' = %s', $value);

        if ($data = $this->db->get_row($query, ARRAY_A)) {
            $this->fill($data);
            $this->put($data[$this->key], $this);
            return $this;
        }

        return null;
    }

    public static function boot()
    {
        self::$collection = new \ArrayObject();
    }

    public function save()
    {
        if (!array_key_exists($this->key, $this->data)) {
            $this->add();
        } else {
            $this->update();
        }

        if (array_key_exists($this->key, $this->data)) {
            $this->put($this->data[$this->key], $this);
        }
    }

    abstract protected function add();

    abstract protected function update();

    public function getID()
    {
        return $this->data[$this->key];
    }
}