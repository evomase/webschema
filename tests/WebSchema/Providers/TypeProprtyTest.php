<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:51
 */

namespace tests\WebSchema\Providers;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Model\Property;
use WebSchema\Model\Type;
use WebSchema\Model\TypeProperty as Model;
use WebSchema\Providers\Property as PropertyProvider;
use WebSchema\Providers\Type as TypeProvider;
use WebSchema\Providers\TypeProperty;
use WebSchema\Utils\Installer;

class TypePropertyTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce();

        Type::boot();
        Property::boot();
        TypeProperty::boot();
    }

    public function testCreateOrDelete()
    {
        $properties = [
            'pid_3' =>
                [
                    Property::FIELD_ID      => 'pid_3',
                    Property::FIELD_COMMENT => '',
                    Property::FIELD_LABEL   => 'id 3',
                    Property::FIELD_RANGES  => ''
                ],

            'pid_0' =>
                [
                    Property::FIELD_ID      => 'pid_0',
                    Property::FIELD_RANGES  => '',
                    Property::FIELD_COMMENT => '',
                    Property::FIELD_LABEL   => 'id 0'
                ]
        ];

        $types = [
            'id_3' =>
                [
                    Type::FIELD_ID      => 'id_3',
                    Type::FIELD_URL     => 'http://www.hotmail.com',
                    Type::FIELD_PARENT  => '',
                    Type::FIELD_COMMENT => '',
                    Type::FIELD_LABEL   => 'id 3',
                    'properties'        => ['pid_3', 'pid_0']
                ]
        ];

        PropertyProvider::createOrUpdate($properties);
        TypeProvider::createOrUpdate($types);

        TypeProperty::createOrUpdate($types);

        Model::clearCollection();

        $this->assertNotNull(Model::get('1'));
        $this->assertNotNull(Model::get('2'));
    }
}