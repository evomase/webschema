<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 13:59
 */

namespace WebSchema\Models\StructuredData\Types\Traits;

use WebSchema\Utils\JsonLD\Node;

trait HasAuthor
{
    /**
     * @param string $name
     * @param string $url
     * @return $this
     */
    protected function setAuthor($name, $url = '')
    {
        if ($url && (filter_var($url, FILTER_VALIDATE_URL) !== false)) {
            $data = [
                'name' => $name,
                'url'  => $url
            ];

            $this->data[static::FIELD_AUTHOR] = new Node('Person', $data);
            return $this;
        }

        return $this->setValue(static::FIELD_AUTHOR, $name);
    }
}