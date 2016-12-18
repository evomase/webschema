<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/12/2016
 * Time: 20:20
 */

namespace WebSchema\Model;


class Property extends Model
{
    const FIELD_COMMENT = 'comment';
    const FIELD_ID = 'id';
    const FIELD_LABEL = 'label';
    const FIELD_RANGES = 'ranges';

    protected static $table = WEB_SCHEMA_TABLE_PROPERTIES;

    protected $data = [
        self::FIELD_ID      => null,
        self::FIELD_COMMENT => null,
        self::FIELD_LABEL   => null,
        self::FIELD_RANGES  => null
    ];

    /**
     * @return false|int
     */
    protected function insert()
    {
        $query = 'INSERT INTO ' . self::$table . ' ( id, comment, label, ranges ) VALUES ( %s, %s, %s, %s )';
        $query = self::$db->prepare($query, $this->data[self::FIELD_ID], $this->data[self::FIELD_COMMENT],
            $this->data[self::FIELD_LABEL], $this->data[self::FIELD_RANGES]);

        return self::$db->query($query);
    }

    /**
     * @return false|int
     */
    protected function update()
    {
        $query = 'UPDATE ' . self::$table . ' SET id = %s, comment = %s, label = %s, ranges = %s WHERE id = %s';
        $query = self::$db->prepare($query, $this->data[self::FIELD_ID], $this->data[self::FIELD_COMMENT],
            $this->data[self::FIELD_LABEL], $this->data[self::FIELD_RANGES], $this->data[self::FIELD_ID]);

        return self::$db->query($query);
    }
}