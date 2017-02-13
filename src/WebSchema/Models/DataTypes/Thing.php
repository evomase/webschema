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
    const FIELD_MAIN_ENTITY_OF_PAGE = 'mainEntityOfPage';

    /**
     * @param string $url
     * @return Model
     */
    public function setMainEntityOfPage($url)
    {
        $this->data[static::FIELD_MAIN_ENTITY_OF_PAGE] = new Node('URL', $url);

        return $this;
    }
}