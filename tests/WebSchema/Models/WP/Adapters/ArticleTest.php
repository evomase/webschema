<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 21/02/2017
 * Time: 16:48
 */

namespace WebSchema\Tests\Models\WP\Adapters;

use WebSchema\Models\WP\Adapters\Article;
use WebSchema\Models\WP\Post;
use WebSchema\Tests\AbstractTestCase;

class ArticleTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        self::setDefaultSettings();
        parent::setUpBeforeClass();

        wp_set_current_user(1);
    }

    public function testGetModifiedDate()
    {
        $post = $this->insertPost();

        $adapter = new Article($post);
        $date = new \DateTime($post->post_modified, new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($date->format('c'), $adapter->getDateModified()->format('c'));

        wp_delete_post($post->ID);
    }

    /**
     * @return \WP_Post
     */
    private function insertPost()
    {
        $post = get_post(wp_insert_post([
            'post_type'    => Post::POST_TYPE,
            'post_title'   => 'ArticleTest',
            'post_status'  => 'publish',
            'post_content' => 'Hello world'
        ]));

        return $post;
    }

    public function testGetModifiedPublished()
    {
        $post = $this->insertPost();

        $adapter = new Article($post);
        $date = new \DateTime($post->post_date, new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($date->format('c'), $adapter->getDatePublished()->format('c'));

        wp_delete_post($post->ID);
    }

    public function testGetImageURL()
    {
        $post = $this->insertPost();

        $upload = wp_upload_bits('AttachmentTestSave.jpg', null,
            file_get_contents(WEB_SCHEMA_DIR . '/tests/resources/images/ModelTest.jpg'));

        $image = wp_insert_attachment([
            'post_mime_type' => $upload['type']
        ], $upload['file'], $post->ID);

        $adapter = new Article($post);
        $this->assertEquals(wp_get_attachment_url($image), $adapter->getImageURL());

        //delete attachment
        wp_delete_attachment($image);

        wp_delete_post($post->ID);
    }

    public function testGetMainEntityOfPage()
    {
        $post = $this->insertPost();
        $adapter = new Article($post);

        $this->assertEquals(get_permalink($post), $adapter->getMainEntityOfPage());

        wp_delete_post($post->ID);
    }

    public function testGetHeadline()
    {
        $post = $this->insertPost();
        $adapter = new Article($post);

        $this->assertEquals(get_the_title($post), $adapter->getHeadline());

        wp_delete_post($post->ID);
    }

    public function testGetAuthorName()
    {
        $post = $this->insertPost();
        $adapter = new Article($post);

        $this->assertEquals(get_userdata($post->post_author)->display_name, $adapter->getAuthorName());

        wp_delete_post($post->ID);
    }

    public function testGetDescription()
    {
        $post = $this->insertPost();
        $adapter = new Article($post);

        $this->assertNotNull($adapter->getDescription());

        wp_delete_post($post->ID);
    }

    public function testGetPublisherName()
    {
        $post = $this->insertPost();
        $adapter = new Article($post);

        $this->assertEquals('Tester', $adapter->getPublisherName());

        wp_delete_post($post->ID);
    }

    public function testGetPublisherImageURL()
    {
        $post = $this->insertPost();
        $adapter = new Article($post);

        $this->assertRegExp('/PublisherImage\.jpg$/i', $adapter->getPublisherImageURL());

        wp_delete_post($post->ID);
    }
}