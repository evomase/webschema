<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:29
 */

namespace tests\WebSchema\Model;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Model\Property;
use WebSchema\Utils\Installer;

class PropertyTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce();

        Property::boot();
    }

    public function testSave()
    {
        $rand = rand(0, 10);

        $model = new Property([
            Property::FIELD_ID      => 'id_' . $rand,
            Property::FIELD_COMMENT => 'random',
            Property::FIELD_LABEL   => 'ID - ' . $rand,
            Property::FIELD_RANGES  => ''
        ]);

        $model->save();

        $this->assertEquals($model, Property::get($model->getID()));

        return $model;
    }

    /**
     * @param Property $model
     * @depends testSave
     */
    public function testGet(Property $model)
    {
        $this->assertInstanceOf('WebSchema\Model\Property', Property::get($model->getID()));

        Property::clearCollection();

        $this->assertInstanceOf('WebSchema\Model\Property', Property::get($model->getID()));
    }
}