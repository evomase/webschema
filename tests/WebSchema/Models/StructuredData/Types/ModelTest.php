<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 15:19
 */

namespace WebSchema\Tests\Models\StructuredData\Types;

use Mockery as m;
use WebSchema\Models\StructuredData\Types\Interfaces\Adapter;
use WebSchema\Models\StructuredData\Types\Model as StructuredDataType;
use WebSchema\Models\Type;
use WebSchema\Tests\AbstractTestCase;

class ModelTest extends AbstractTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testGetImage()
    {
        //test local
        $url = WEB_SCHEMA_DIR_URL . '/tests/resources/images/ModelTest.jpg';

        $adapter = m::mock(Adapter::class);

        /**
         * @var StructuredDataType $model
         */
        $model = m::mock(StructuredDataType::class, [$adapter])->makePartial();
        $image = $model->getImage($url);

        $this->assertNotEmpty($image);
        $this->assertEquals($url, $image['url']);
        $this->assertEquals(700, $image['width']);
        $this->assertEquals(2, $image['height']);

        //test public
        $url = 'http://placehold.it/701x3.jpg';
        $image = $model->getImage($url);

        $this->assertNotEmpty($image);
        $this->assertEquals($url, $image['url']);
        $this->assertEquals(701, $image['width']);
        $this->assertEquals(3, $image['height']);
    }

    public function testGetName()
    {
        $adapter = m::mock(Adapter::class);
        $type = 'Article';

        /**
         * @var StructuredDataType $model
         */
        $model = m::namedMock($type, StructuredDataType::class, [$adapter])->makePartial();
        $this->assertEquals('Article', $model::getName());
    }

    public function testGetSchema()
    {
        $type = 'Article';

        $adapter = m::mock(Adapter::class);

        $typeMock = m::mock(Type::class)->makePartial();
        $typeMock->shouldReceive('get')
            ->once()
            ->with($type)
            ->andReturn(new Type());

        /**
         * @var Type $typeMock
         */
        $typeMock::boot();

        /**
         * @var StructuredDataType $model
         */
        $model = m::namedMock($type, StructuredDataType::class, [$adapter])->makePartial();
        $model::setTypeClass(get_class($typeMock));

        $this->assertInstanceOf(Type::class, $model::getSchema());

        //revert back to default
        $model::setTypeClass(Type::class);
    }
}