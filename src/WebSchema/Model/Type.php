<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/12/2016
 * Time: 20:18
 */

namespace WebSchema\Model;


class Type extends Model
{
    const FIELD_COMMENT = 'comment';
    const FIELD_ID = 'id';
    const FIELD_LABEL = 'label';
    const FIELD_PARENT = 'parent';
    const FIELD_URL = 'url';

    protected static $table = WEB_SCHEMA_TABLE_TYPES;

    protected $data = [
        self::FIELD_COMMENT => null,
        self::FIELD_LABEL   => null,
        self::FIELD_URL     => null,
        self::FIELD_PARENT  => 0,
        self::FIELD_ID      => null
    ];

    private $properties;

    /**
     * @return array
     */
    public function getProperties()
    {
        if (empty($this->properties)) {
            $this->properties = TypeProperty::search($this->data[self::FIELD_ID], TypeProperty::FIELD_TYPE_ID);
        }

        return $this->properties;
    }

    /**
     * @return false|int
     */
    protected function insert()
    {
        $query = 'INSERT INTO ' . self::$table . ' ( id, comment, label, url, parent ) VALUES ( %s, %s, %s, %s, %s )';
        $query = self::$db->prepare($query, $this->data[self::FIELD_ID], $this->data[self::FIELD_COMMENT],
            $this->data[self::FIELD_LABEL], $this->data[self::FIELD_URL], $this->data[self::FIELD_PARENT]);

        return self::$db->query($query);
    }

    /**
     * @return false|int
     */
    protected function update()
    {
        $query = 'UPDATE ' . self::$table . ' SET id = %s, comment = %s, label = %s, url = %s, parent = %s WHERE id = %s';
        $query = self::$db->prepare($query, $this->data[self::FIELD_ID], $this->data[self::FIELD_COMMENT],
            $this->data[self::FIELD_LABEL], $this->data[self::FIELD_URL], $this->data[self::FIELD_PARENT],
            $this->data[self::FIELD_ID]);

        return self::$db->query($query);
    }
}