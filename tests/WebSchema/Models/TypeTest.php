<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:29
 */

namespace tests\WebSchema\Models;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Models\Type;
use WebSchema\Utils\Installer;

class TypeTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->disableImport()->runOnce();

        Type::boot();
    }

    public function testSave()
    {
        $model = new Type([
            Type::FIELD_ID        => 'id_1',
            Type::FIELD_COMMENT   => 'random',
            Type::FIELD_LABEL     => 'ID - 1',
            Type::FIELD_ANCESTORS => ['Thing'],
            Type::FIELD_URL       => 'http://www.hotmail.com'
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
        $this->assertInstanceOf(Type::class, Type::get($model->getID()));

        Type::clearCollection();

        $model = Type::get($model->getID());

        $this->assertInstanceOf(Type::class, $model);
        $this->assertInternalType('array', $model->getAncestors());
    }

    public function testFill()
    {
        $model = new Type();
        $model->fill([
            Type::FIELD_ID        => 'id_1',
            Type::FIELD_COMMENT   => 'random',
            Type::FIELD_LABEL     => 'ID - 1',
            Type::FIELD_ANCESTORS => json_encode(['Thing']),
            Type::FIELD_URL       => 'http://www.hotmail.com'
        ]);

        $this->assertInternalType('array', $model->getAncestors());

        $model->fill([
            Type::FIELD_ANCESTORS => 'ahahahha agagg a'
        ]);

        $this->assertEquals(['Thing'], $model->getAncestors());
    }

    public function testToArray()
    {
        $model = new Type([
            Type::FIELD_ID        => 'id_2',
            Type::FIELD_COMMENT   => 'random',
            Type::FIELD_LABEL     => 'ID - 2',
            Type::FIELD_ANCESTORS => ['Thing'],
            Type::FIELD_URL       => 'http://www.hotmail.com'
        ]);

        $model->save();
        $model = $model->toArray();

        $this->assertInternalType('array', $model);
        $this->assertArrayHasKey(Type::FIELD_URL, $model);
    }
}