<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:51
 */

namespace WebSchema\Tests\Factory;

use WebSchema\Factory\PropertyFactory;
use WebSchema\Factory\TypeFactory;
use WebSchema\Factory\TypePropertyFactory;
use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty as Model;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\Installer;

class TypePropertyFactoryTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        (new Installer())->disableImport()->runOnce();
    }

    public function testCreateOrDelete()
    {
        $properties = [
            'pid_3' =>
                [
                    Property::FIELD_ID      => 'pid_3',
                    Property::FIELD_COMMENT => '',
                    Property::FIELD_LABEL   => 'id 3',
                    Property::FIELD_RANGES  => []
                ],

            'pid_0' =>
                [
                    Property::FIELD_ID      => 'pid_0',
                    Property::FIELD_RANGES  => [],
                    Property::FIELD_COMMENT => '',
                    Property::FIELD_LABEL   => 'id 0'
                ]
        ];

        $types = [
            'id_3' =>
                [
                    Type::FIELD_ID        => 'id_3',
                    Type::FIELD_URL       => 'http://www.hotmail.com',
                    Type::FIELD_ANCESTORS => [],
                    Type::FIELD_COMMENT   => '',
                    Type::FIELD_LABEL     => 'id 3',
                    'properties'          => ['pid_3', 'pid_0']
                ]
        ];

        PropertyFactory::createOrUpdate($properties);
        TypeFactory::createOrUpdate($types);

        TypePropertyFactory::createOrUpdate($types);

        Model::clearCollection();

        $this->assertNotNull(Model::get('1'));
        $this->assertNotNull(Model::get('2'));
    }
}