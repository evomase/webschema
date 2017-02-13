<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 19:52
 */

namespace WebSchema\Utils;

use WebSchema\Utils\JsonLD\Node;

class JsonLD
{
    private function __construct()
    {
    }

    /**
     * @param Node $node
     * @return string|null
     */
    public static function create(Node $node)
    {
        if ($data = $node->toArray()) {
            $json['@context'] = 'http://schema.org/';
            $json += $data;

            return json_encode($json);
        }

        return null;
    }
}