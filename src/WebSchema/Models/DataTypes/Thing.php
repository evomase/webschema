<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 13/02/2017
 * Time: 17:37
 */

namespace WebSchema\Models\DataTypes;

use WebSchema\Utils\JsonLD\Node;

abstract class Thing extends Model
{
    const FIELD_DESCRIPTION = 'description';
    const FIELD_IMAGE = 'image';
    const FIELD_MAIN_ENTITY_OF_PAGE = 'mainEntityOfPage';
    const FIELD_NAME = 'name';

    /**
     * @param string $url
     * @return $this
     */
    protected function setMainEntityOfPage($url)
    {
        $this->data[static::FIELD_MAIN_ENTITY_OF_PAGE] = new Node('URL', $url);

        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    protected function setImage($url)
    {
        if ($image = $this->getImage($url)) {
            return $this->setValue(static::FIELD_IMAGE, $image);
        }

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    protected function setName($name)
    {
        $this->data[static::FIELD_NAME] = $name;

        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    protected function setDescription($description)
    {
        return $this->setValue(static::FIELD_DESCRIPTION, $description);
    }
}