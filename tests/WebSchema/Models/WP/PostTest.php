<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 10:53
 */

namespace WebSchema\Tests\Models\WP;

use WebSchema\Models\Type;
use WebSchema\Models\WP\Post;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\Installer;

class PostTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        self::setDefaultSettings();
        parent::setUpBeforeClass();

        (new Installer())->runOnce();

        wp_set_current_user(1);
    }

    public function testSave()
    {
        $type = 'BlogPosting';

        $_POST[Post::META_KEY] = [
            Post::FIELD_DATA_TYPE => Type::get($type)->getID()
        ];

        $post = get_post(wp_insert_post([
            'post_title'  => 'PostTest',
            'post_status' => 'publish'
        ]));

        $model = Post::get($post->ID);

        $this->assertEquals($model->getData()[Post::FIELD_DATA_TYPE], $type);

        //test update
        $type = 'Article';

        $_POST[Post::META_KEY] = [
            Post::FIELD_DATA_TYPE => Type::get($type)->getID()
        ];

        wp_update_post($post);

        //clear collection to retrieve info from db
        Post::clearCollection();

        $model = Post::get($post->ID);

        $this->assertEquals($model->getData()[Post::FIELD_DATA_TYPE], $type);

        return $post;
    }

    /**
     * @depends testSave
     * @param \WP_Post $post
     */
    public function testRenderMetaBox(\WP_Post $post)
    {
        $this->expectOutputRegex('/<label for="web-schema-data-type" class="post-attributes-label">Data Type<\/label>/');
        do_action('add_meta_boxes');
        do_meta_boxes(Post::POST_TYPE, 'advanced', $post);

        wp_delete_post($post->ID);
    }

    public function testGetJson()
    {
        $type = 'Article';

        $_POST[Post::META_KEY] = [
            Post::FIELD_DATA_TYPE => Type::get($type)->getID()
        ];

        $post = get_post(wp_insert_post([
            'post_title'  => 'PostTest 2',
            'post_status' => 'publish'
        ]));

        $upload = wp_upload_bits('PostTest2.jpg', null,
            file_get_contents(WEB_SCHEMA_DIR . '/tests/resources/images/ModelTest.jpg'));

        $image = wp_insert_attachment([
            'post_mime_type' => $upload['type']
        ], $upload['file'], $post->ID);

        $this->assertInternalType('array', json_decode(Post::get($post->ID)->getJson(), true));

        wp_delete_attachment($image);
        wp_delete_post($post->ID);
    }
}