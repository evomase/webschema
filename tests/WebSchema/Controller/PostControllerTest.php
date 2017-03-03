<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 18:32
 */

namespace WebSchema\Tests\Controller;

use WebSchema\Models\Type;
use WebSchema\Models\WP\Post;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Tests\Traits\RequiresSchema;
use WebSchema\Utils\Installer;

class PostControllerTest extends AbstractTestCase
{
    use RequiresSchema;

    public static function setUpBeforeClass()
    {
        parent::setDefaultSettings();

        parent::setUpBeforeClass();

        self::createDummySchema();

        (new Installer([
            Installer::OPTION_SCHEMA_PATH => self::$schemaPath
        ]))->runOnce();

        wp_set_current_user(1);
    }

    public function testPrintJSON()
    {
        global $post;

        $type = 'BlogPosting';

        $_POST[Post::META_KEY] = [
            Post::FIELD_DATA_TYPE => Type::get($type)->getID()
        ];

        $post = get_post(wp_insert_post([
            'post_title'  => 'PostControllerTest',
            'post_status' => 'publish'
        ]));

        $this->expectOutputRegex('/application\/ld\+json/i');

        do_action('wp_head');

        $this->assertCount(1, ob_get_status(true));
    }
}