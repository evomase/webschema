<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:29
 */

namespace WebSchema\Tests\Models;

use WebSchema\Models\Property;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\Installer;

class PropertyTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        (new Installer())->disableImport()->runOnce();
    }

    public function testSave()
    {
        $model = new Property([
            Property::FIELD_ID      => 'id_0',
            Property::FIELD_COMMENT => 'random',
            Property::FIELD_LABEL   => 'ID - 0',
            Property::FIELD_RANGES  => ['Thing']
        ]);

        $model->save();

        $this->assertEquals($model, Property::get($model->getID()));

        return $model;
    }

    public function testFill()
    {
        $model = new Property();

        $model->fill([
            Property::FIELD_ID      => 'id_1',
            Property::FIELD_COMMENT => 'random',
            Property::FIELD_LABEL   => 'ID - 1',
            Property::FIELD_RANGES  => ['Thing']
        ]);

        $this->assertInternalType('array', $model->getRanges());

        $model->fill([
            Property::FIELD_RANGES => 'hjahahhaha'
        ]);

        $this->assertEquals(['Thing'], $model->getRanges());
    }

    /**
     * @param Property $model
     * @depends testSave
     */
    public function testGet(Property $model)
    {
        $this->assertInstanceOf(Property::class, Property::get($model->getID()));

        Property::clearCollection();

        $this->assertInstanceOf(Property::class, Property::get($model->getID()));
    }
}