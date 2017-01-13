<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 20:21
 */

namespace WebSchema\Model;

class TypeProperty extends Model
{
    const FIELD_ID = 'id';
    const FIELD_PROPERTY_ID = 'property_id';
    const FIELD_TYPE_ID = 'type_id';

    protected static $table = WEB_SCHEMA_TABLE_TYPE_PROPERTIES;

    protected $data = [
        self::FIELD_ID          => null,
        self::FIELD_PROPERTY_ID => null,
        self::FIELD_TYPE_ID     => null
    ];

    /**
     * @param $type
     * @param $property
     * @return TypeProperty|null
     */
    public static function lookup($type, $property)
    {
        $model = null;
        $query = static::$db->prepare('SELECT * FROM ' . static::$table . ' WHERE type_id = %s AND property_id = %s',
            $type, $property);

        if ($data = static::$db->get_col($query, ARRAY_A)) {
            $model = new self($data);
            $model->new = false;

            $model->put($data[static::$key], $model);
        }

        return $model;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->data[self::FIELD_PROPERTY_ID];
    }

    /**
     * @return false|int
     */
    protected function insert()
    {
        $query = 'INSERT INTO ' . self::$table . ' ( type_id, property_id ) VALUES ( %s, %s )';
        $query = self::$db->prepare($query, $this->data[self::FIELD_TYPE_ID], $this->data[self::FIELD_PROPERTY_ID]);

        return $this->data[self::FIELD_ID] = self::$db->query($query);
    }

    /**
     * @return false|int
     */
    protected function update()
    {
        $query = 'UPDATE ' . self::$table . ' SET type_id = %s, property_id = %s WHERE id = %s';
        $query = self::$db->prepare($query, $this->data[self::FIELD_TYPE_ID], $this->data[self::FIELD_PROPERTY_ID],
            $this->data[self::FIELD_ID]);

        return self::$db->query($query);
    }
}