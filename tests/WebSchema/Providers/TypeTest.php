<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:51
 */

namespace tests\WebSchema\Providers;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Model\Type as Model;
use WebSchema\Providers\Type;
use WebSchema\Utils\Installer;

class TypeTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce();
        Type::boot();
    }

    public function testCreateOrDelete()
    {
        $data = [
            'id_3' =>
                [
                    Model::FIELD_ID      => 'id_3',
                    Model::FIELD_URL     => 'http://www.hotmail.com',
                    Model::FIELD_PARENT  => 'id_2',
                    Model::FIELD_COMMENT => '',
                    Model::FIELD_LABEL   => 'id 3'
                ],

            'id_0' =>
                [
                    Model::FIELD_ID      => 'id_0',
                    Model::FIELD_URL     => 'http://www.hotmail.com',
                    Model::FIELD_PARENT  => null,
                    Model::FIELD_COMMENT => '',
                    Model::FIELD_LABEL   => 'id 0'
                ],

            'id_1' =>
                [
                    Model::FIELD_ID      => 'id_1',
                    Model::FIELD_URL     => 'http://www.hotmail.com',
                    Model::FIELD_PARENT  => 'id_0',
                    Model::FIELD_COMMENT => '',
                    Model::FIELD_LABEL   => 'id 1'
                ],

            'id_2' =>
                [
                    Model::FIELD_ID      => 'id_2',
                    Model::FIELD_URL     => 'http://www.hotmail.com',
                    Model::FIELD_PARENT  => 'id_1',
                    Model::FIELD_COMMENT => '',
                    Model::FIELD_LABEL   => 'id 2'
                ]
        ];

        $this->assertEquals(Type::createOrUpdate($data), 0);

        Model::clearCollection();

        $this->assertNotNull(Model::get('id_0'));
        $this->assertNotNull(Model::get('id_1'));
        $this->assertNotNull(Model::get('id_2'));
        $this->assertNotNull(Model::get('id_3'));
    }
}