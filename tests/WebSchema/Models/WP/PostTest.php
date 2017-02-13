<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 10:53
 */

namespace tests\WebSchema\Models\WP;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty;
use WebSchema\Models\WP\Post;
use WebSchema\Utils\Installer;

class PostTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        //Boot required classes
        Type::boot();
        TypeProperty::boot();
        Property::boot();
        Post::boot();

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

        Post::renderMetaBox($post);
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

        //print_r(Post::get($post->ID)->getJson());
    }
}