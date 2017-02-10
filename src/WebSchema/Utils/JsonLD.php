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
     * @param string $type
     * @param Node[] $nodes
     * @return string
     */
    public static function create($type, array $nodes)
    {
        $json['@context'] = 'http://schema.org/';
        $json['@type'] = $type;

        $json = array_merge($json, self::add($nodes));

        print_r($json);

        //return json_encode($json);
    }

    /**
     * @param Node[] $nodes
     * @param bool   $child
     * @return array
     */
    private static function add(array $nodes, $child = false)
    {
        $json = [];

        foreach ($nodes as $node) {
            /**
             * @var Node $node
             */

            $property = $node->getProperty();

            if ($data = $node->getData()) {
                if (is_array($data)) {
                    $data = array_merge(['@type' => $node->getType()], $data);
                }

                if ($child) {
                    $json[] = $data;
                } else {
                    $json[$property] = $data;
                }

                //don't process children
                continue;
            }

            if ($children = $node->getChildren()) {
                $json[$property] = self::add($children, true);
            }
        }

        return $json;
    }
}