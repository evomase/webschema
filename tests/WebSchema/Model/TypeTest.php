<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:29
 */

namespace tests\WebSchema\Model;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Model\Type;
use WebSchema\Utils\Installer;

class TypeTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce();

        Type::boot();
    }

    public function testSave()
    {
        $rand = rand(0, 10);

        $model = new Type([
            Type::FIELD_ID      => 'id_' . $rand,
            Type::FIELD_COMMENT => 'random',
            Type::FIELD_LABEL   => 'ID - ' . $rand,
            Type::FIELD_PARENT  => '',
            Type::FIELD_URL     => 'http://www.hotmail.com'
        ]);

        $model->save();

        $this->assertEquals($model, Type::get($model->getID()));

        return $model;
    }

    /**
     * @param Type $model
     * @depends testSave
     */
    public function testGet(Type $model)
    {
        $this->assertInstanceOf('WebSchema\Model\Type', Type::get($model->getID()));

        Type::clearCollection();

        $this->assertInstanceOf('WebSchema\Model\Type', Type::get($model->getID()));
    }
}