<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/03/2017
 * Time: 10:44
 */

namespace WebSchema\Tests\Traits;

use WebSchema\Models\StructuredData\StructuredData;
use WebSchema\Utils\Installer;

trait RequiresSchema
{
    private static $schemaPath = WEB_SCHEMA_DIR . '/tests/resources/migration/schema.json';

    protected static function createDummySchema()
    {
        $registeredTypes = array_keys(StructuredData::getTypes());

        if (!file_exists(self::$schemaPath)) {
            $data = json_decode(file_get_contents(Installer::MIGRATION_DIRECTORY . '/schema.json'), true);

            $json = [
                'datatypes'  => $data['datatypes'],
                'properties' => [],
                'types'      => []
            ];

            $types = $data['types'];
            $properties = $data['properties'];

            foreach ($registeredTypes as $type) {
                $json = self::addSchemaType($type, $types, $properties, $json);
            }

            file_put_contents(self::$schemaPath, json_encode($json));
        }
    }

    /**
     * @param string $type
     * @param array  $types
     * @param array  $properties
     * @param array  $json
     * @return array
     */
    protected static function addSchemaType($type, array $types, array $properties, array $json)
    {
        if (!empty($types[$type])) {
            $type = $json['types'][$type] = $types[$type];

            foreach ($type['properties'] as $property) {
                if (empty($json['properties'][$property])) {
                    $property = $json['properties'][$property] = $properties[$property];

                    //add ranges (which are types too)
                    foreach ($property['ranges'] as $range) {
                        $json = self::addSchemaType($range, $types, $properties, $json);
                    }
                }
            }
        }

        return $json;
    }
}