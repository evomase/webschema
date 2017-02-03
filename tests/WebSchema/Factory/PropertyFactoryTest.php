<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:51
 */

namespace tests\WebSchema\Factory;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Models\Property;
use WebSchema\Factory\PropertyFactory;
use WebSchema\Utils\Installer;

class PropertyFactoryTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->disableImport()->runOnce();
        PropertyFactory::boot();
    }

    public function testCreateOrDelete()
    {
        $data = [
            'id_3' =>
                [
                    Property::FIELD_ID      => 'id_3',
                    Property::FIELD_COMMENT => '',
                    Property::FIELD_LABEL   => 'id 3',
                    Property::FIELD_RANGES  => ['Thing']
                ],

            'id_0' =>
                [
                    Property::FIELD_ID      => 'id_0',
                    Property::FIELD_RANGES  => ['Thing'],
                    Property::FIELD_COMMENT => '',
                    Property::FIELD_LABEL   => 'id 0'
                ]
        ];

        PropertyFactory::createOrUpdate($data);

        Property::clearCollection();

        $this->assertNotNull(Property::get('id_0'));
        $this->assertNotNull(Property::get('id_3'));
    }
}