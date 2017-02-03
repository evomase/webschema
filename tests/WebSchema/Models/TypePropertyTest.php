<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 18:29
 */

namespace tests\WebSchema\Models;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty;
use WebSchema\Utils\Installer;

class TypePropertyTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        (new Installer())->disableImport()->runOnce();

        TypeProperty::boot();
    }

    public function testSave()
    {
        $type = new Type([
            Type::FIELD_ID        => 'id_0',
            Type::FIELD_COMMENT   => 'random',
            Type::FIELD_LABEL     => 'ID - 0',
            Type::FIELD_ANCESTORS => ['Thing'],
            Type::FIELD_URL       => 'http://www.hotmail.com'
        ]);

        $type->save();

        $property = new Property([
            Property::FIELD_ID      => 'id_0',
            Property::FIELD_COMMENT => 'random',
            Property::FIELD_LABEL   => 'ID - 0',
            Property::FIELD_RANGES  => ['Thing']
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
        $this->assertInstanceOf(TypeProperty::class, TypeProperty::get($model->getID()));

        TypeProperty::clearCollection();

        $this->assertInstanceOf(TypeProperty::class, TypeProperty::get($model->getID()));
    }
}