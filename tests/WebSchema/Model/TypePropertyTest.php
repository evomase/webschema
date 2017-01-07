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
use WebSchema\Model\Type;
use WebSchema\Model\TypeProperty;
use WebSchema\Utils\Installer;

class TypePropertyTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->runOnce(false);

        TypeProperty::boot();
    }

    public function testSave()
    {
        $rand = rand(0, 10);

        $type = new Type([
            Type::FIELD_ID      => 'id_' . $rand,
            Type::FIELD_COMMENT => 'random',
            Type::FIELD_LABEL   => 'ID - ' . $rand,
            Type::FIELD_PARENT  => '',
            Type::FIELD_URL     => 'http://www.hotmail.com'
        ]);

        $type->save();

        $property = new Property([
            Property::FIELD_ID      => 'id_' . $rand,
            Property::FIELD_COMMENT => 'random',
            Property::FIELD_LABEL   => 'ID - ' . $rand,
            Property::FIELD_RANGES  => ''
        ]);

        $property->save();

        $model = new TypeProperty([
            TypeProperty::FIELD_TYPE_ID     => $type->getID(),
            TypeProperty::FIELD_PROPERTY_ID => $property->getID()
        ]);

        $model->save();

        $this->assertEquals($model, TypeProperty::get($model->getID()));

        return $model;
    }

    /**
     * @param TypeProperty $model
     * @depends testSave
     */
    public function testGet(TypeProperty $model)
    {
        $this->assertInstanceOf('WebSchema\Model\TypeProperty', TypeProperty::get($model->getID()));

        TypeProperty::clearCollection();

        $this->assertInstanceOf('WebSchema\Model\TypeProperty', TypeProperty::get($model->getID()));
    }
}