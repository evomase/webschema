<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:51
 */

namespace tests\WebSchema\Providers;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Model\Property as Model;
use WebSchema\Providers\Property;
use WebSchema\Utils\Installer;

class PropertyTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce();
        Property::boot();
    }

    public function testCreateOrDelete()
    {
        $data = [
            'id_3' =>
                [
                    Model::FIELD_ID      => 'id_3',
                    Model::FIELD_COMMENT => '',
                    Model::FIELD_LABEL   => 'id 3',
                    Model::FIELD_RANGES  => ''
                ],

            'id_0' =>
                [
                    Model::FIELD_ID      => 'id_0',
                    Model::FIELD_RANGES  => '',
                    Model::FIELD_COMMENT => '',
                    Model::FIELD_LABEL   => 'id 0'
                ]
        ];

        Property::createOrUpdate($data);

        Model::clearCollection();

        $this->assertNotNull(Model::get('id_0'));
        $this->assertNotNull(Model::get('id_3'));
    }
}