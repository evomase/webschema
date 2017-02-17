<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/02/2017
 * Time: 17:12
 */

namespace WebSchema\Tests\Models\DataTypes;

use Mockery as m;
use WebSchema\Models\DataTypes\Article;
use WebSchema\Models\DataTypes\Interfaces\ArticleAdapter;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\Installer;

class ArticleTest extends AbstractTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        (new Installer())->runOnce();
    }

    public function testGenerateJson()
    {
        $date = new \DateTime();
        $image = WEB_SCHEMA_DIR_URL . '/tests/resources/images/ModelTest.jpg';

        $adapter = m::mock(ArticleAdapter::class)->makePartial();
        $adapter->shouldReceive('getDateModified')->andReturn($date);
        $adapter->shouldReceive('getImageURL')->andReturn($image);
        $adapter->shouldReceive('getDatePublished')->andReturn($date);
        $adapter->shouldReceive('getPublisherName')->andReturn('Publisher');
        $adapter->shouldReceive('getPublisherImageURL')->andReturn($image);
        $adapter->shouldReceive('getMainEntityOfPage')->andReturn('http://www.google.com');
        $adapter->shouldReceive('getHeadline')->andReturn('Headline');
        $adapter->shouldReceive('getAuthor')->andReturn('Author');
        $adapter->shouldReceive('getDescription')->andReturn('Description');

        /**
         * @var ArticleAdapter $adapter
         */
        $type = new Article($adapter);
        $this->assertJson($type->generateJSON());
    }
}