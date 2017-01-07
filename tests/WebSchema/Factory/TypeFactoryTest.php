<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:51
 */

namespace tests\WebSchema\Factory;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Factory\TypeFactory;
use WebSchema\Model\Type;
use WebSchema\Utils\Installer;

class TypeFactoryTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce(false);
        TypeFactory::boot();
    }

    public function testCreateOrDelete()
    {
        $data = [
            'id_3' =>
                [
                    Type::FIELD_ID      => 'id_3',
                    Type::FIELD_URL     => 'http://www.hotmail.com',
                    'ancestors'         => ['id_2'],
                    Type::FIELD_COMMENT => '',
                    Type::FIELD_LABEL   => 'id 3'
                ],

            'id_0' =>
                [
                    Type::FIELD_ID      => 'id_0',
                    Type::FIELD_URL     => 'http://www.hotmail.com',
                    Type::FIELD_COMMENT => '',
                    Type::FIELD_LABEL   => 'id 0'
                ],

            'id_1' =>
                [
                    Type::FIELD_ID      => 'id_1',
                    Type::FIELD_URL     => 'http://www.hotmail.com',
                    'ancestors'         => ['id_0'],
                    Type::FIELD_COMMENT => '',
                    Type::FIELD_LABEL   => 'id 1'
                ],

            'id_2' =>
                [
                    Type::FIELD_ID      => 'id_2',
                    Type::FIELD_URL     => 'http://www.hotmail.com',
                    'ancestors'         => ['id_1'],
                    Type::FIELD_COMMENT => '',
                    Type::FIELD_LABEL   => 'id 2'
                ]
        ];

        $this->assertEquals(TypeFactory::createOrUpdate($data), 0);

        Type::clearCollection();

        $this->assertNotNull(Type::get('id_0'));
        $this->assertNotNull(Type::get('id_1'));
        $this->assertNotNull(Type::get('id_2'));
        $this->assertNotNull(Type::get('id_3'));
    }

    public function testGetAll()
    {
        $data = TypeFactory::getAll();

        $this->assertCount(4, $data);
        $this->assertInternalType('array', current($data));
        $this->assertArrayHasKey(Type::FIELD_URL, current($data));
    }
}