<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/02/2017
 * Time: 17:12
 */

namespace WebSchema\Tests\Models\StructuredData\Types;

use Mockery as m;
use WebSchema\Models\StructuredData\Types\Article;
use WebSchema\Models\StructuredData\Types\Interfaces\ArticleAdapter;
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
        $base = WEB_SCHEMA_DIR_URL . '/tests/resources/images';
        $imageURL = $base . '/ModelTest.jpg';
        $publisherImageURL = $base . '/PublisherImage.jpg';

        $adapter = m::mock(ArticleAdapter::class)->makePartial();
        $adapter->shouldReceive('getDateModified')->andReturn($date);
        $adapter->shouldReceive('getImageURL')->andReturn($imageURL);
        $adapter->shouldReceive('getDatePublished')->andReturn($date);
        $adapter->shouldReceive('getPublisherName')->andReturn('Publisher');
        $adapter->shouldReceive('getPublisherImageURL')->andReturn($publisherImageURL);
        $adapter->shouldReceive('getMainEntityOfPage')->andReturn('http://www.google.com');
        $adapter->shouldReceive('getHeadline')->andReturn('Headline');
        $adapter->shouldReceive('getAuthorName')->andReturn('Author');
        $adapter->shouldReceive('getAuthorURL')->andReturn('http://www.google.com');
        $adapter->shouldReceive('getDescription')->andReturn('Description');

        /**
         * @var ArticleAdapter $adapter
         */
        $type = new Article($adapter);
        $json = $type->generateJSON();
        $this->assertJson($json);

        $json = json_decode($json, true);

        foreach ($json as $index => $item) {
            $this->assertNotEmpty($item);
        }

        $this->assertEquals('ImageObject', $json[Article::FIELD_IMAGE]['@type']);
        $this->assertEquals($imageURL, $json[Article::FIELD_IMAGE]['url']);

        $this->assertEquals('ImageObject', $json[Article::FIELD_PUBLISHER]['logo']['@type']);
        $this->assertEquals($publisherImageURL, $json[Article::FIELD_PUBLISHER]['logo']['url']);
    }

    /**
     * For required values
     */
    public function testGenerateJsonException()
    {
        $date = new \DateTime();
        $adapter = m::mock(ArticleAdapter::class)->makePartial();
        $adapter->shouldReceive('getDateModified')->andReturn($date);
        $adapter->shouldReceive('getImageURL')->andReturn(null);
        $adapter->shouldReceive('getDatePublished')->andReturn($date);
        $adapter->shouldReceive('getPublisherName')->andReturn(null);
        $adapter->shouldReceive('getPublisherImageURL')->andReturn(null);
        $adapter->shouldReceive('getMainEntityOfPage')->andReturn('http://www.google.com');
        $adapter->shouldReceive('getHeadline')->andReturn('Headline');
        $adapter->shouldReceive('getAuthorName')->andReturn('Author');
        $adapter->shouldReceive('getAuthorURL')->andReturn('http://www.google.com');
        $adapter->shouldReceive('getDescription')->andReturn('Description');

        /**
         * @var ArticleAdapter $adapter
         */
        $type = new Article($adapter);

        $this->setExpectedException(\UnexpectedValueException::class);
        $type->generateJSON();
    }
}