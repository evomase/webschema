<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 13/02/2017
 * Time: 17:37
 */

namespace WebSchema\Models\DataTypes;

use WebSchema\Utils\JsonLD\Node;

class Thing extends Model
{
    const FIELD_AUTHOR = 'author';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_HEADLINE = 'headline';
    const FIELD_IMAGE = 'image';
    const FIELD_MAIN_ENTITY_OF_PAGE = 'mainEntityOfPage';

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
     * @param string $name
     * @return $this
     */
    protected function setAuthor($name)
    {
        return $this->setValue(static::FIELD_AUTHOR, $name);
    }

    /**
     * @param string $headline
     * @return $this
     */
    protected function setHeadline($headline)
    {
        return $this->setValue(static::FIELD_HEADLINE, $headline);
    }

    /**
     * @param string $description
     * @return $this
     */
    protected function setDescription($description)
    {
        return $this->setValue(static::FIELD_DESCRIPTION, $description);
    }

    /**
     * @param $url
     * @return $this
     */
    protected function setImage($url)
    {
        if ($image = $this->getImage($url)) {
            print_r($image);

            return $this->setValue(static::FIELD_IMAGE, $image);
        }

        return $this;
    }
}