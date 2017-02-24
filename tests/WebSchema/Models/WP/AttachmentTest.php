<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 18:20
 */

namespace WebSchema\Tests\Models\WP;

use WebSchema\Models\StructuredData\Types\Article;
use WebSchema\Models\Type;
use WebSchema\Models\WP\Post;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\Installer;

class AttachmentTest extends AbstractTestCase
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
        $type = 'Article';

        $_POST[Post::META_KEY] = [
            Post::FIELD_DATA_TYPE => Type::get($type)->getID()
        ];

        $post = get_post(wp_insert_post([
            'post_title'  => 'AttachmentTest',
            'post_status' => 'publish'
        ]));

        $upload = wp_upload_bits('AttachmentTestSave.jpg', null,
            file_get_contents(WEB_SCHEMA_DIR . '/tests/resources/images/ModelTest.jpg'));

        $image = wp_insert_attachment([
            'post_mime_type' => $upload['type']
        ], $upload['file'], $post->ID);

        $data = json_decode(Post::get($post->ID)->getJson(), true);

        $this->assertNotEmpty($data[Article::FIELD_IMAGE]);
        $this->assertRegExp('/AttachmentTestSave/i', $data[Article::FIELD_IMAGE]['url']);

        //delete attachment
        wp_delete_attachment($image);

        $data = json_decode(Post::get($post->ID)->getJson(), true);
        $this->assertArrayNotHasKey(Article::FIELD_IMAGE, $data);

        wp_delete_post($post->ID);
    }
}