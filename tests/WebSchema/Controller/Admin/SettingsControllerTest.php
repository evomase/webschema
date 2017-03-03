<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 12:20
 */

namespace WebSchema\Tests\Controller\Admin;

use WebSchema\Controllers\Admin\SettingsController;
use WebSchema\Models\WP\Settings;

class SettingsControllerTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        new SettingsController();

        wp_set_current_user(1);
    }

    public static function tearDownAfterClass()
    {
        $_POST = $_FILES = [];
    }

    public function testAddMenu()
    {
        do_action('admin_menu');
        $this->assertTrue(has_action('admin_page_' . SettingsController::SLUG));
    }

    public function testGet()
    {
        $this->expectOutputRegex('/<h1>Web Schema Settings<\/h1>/');
        do_action('admin_page_' . SettingsController::SLUG);
    }

    public function testSanitize()
    {
        $file = WEB_SCHEMA_DIR . '/tests/resources/images/SettingsControllerTest.jpg';
        copy(WEB_SCHEMA_DIR . '/tests/resources/images/PublisherImage.jpg', $file);

        $_FILES[Settings::NAME] = [
            'name' => [
                Settings::FIELD_PUBLISHER => [
                    Settings::FIELD_PUBLISHER_LOGO => 'SettingsControllerTest.jpg'
                ]
            ],

            'type' => [
                Settings::FIELD_PUBLISHER => [
                    Settings::FIELD_PUBLISHER_LOGO => 'image/jpeg'
                ]
            ],

            'tmp_name' => [
                Settings::FIELD_PUBLISHER => [
                    Settings::FIELD_PUBLISHER_LOGO => $file
                ]
            ],

            'error' => [
                Settings::FIELD_PUBLISHER => [
                    Settings::FIELD_PUBLISHER_LOGO => 0
                ]
            ],

            'size' => [
                Settings::FIELD_PUBLISHER => [
                    Settings::FIELD_PUBLISHER_LOGO => filesize($file)
                ]
            ]

        ];

        //needed to handle file upload
        $_POST['action'] = 'update';

        $data = apply_filters('sanitize_option_' . Settings::NAME, []);

        $this->assertEquals(0, $data[Settings::FIELD_AMP][Settings::FIELD_AMP_USE_SSL]);
        $this->assertArrayHasKey(Settings::SECTION_POST_TYPES, $data);

        $logo = $data[Settings::FIELD_PUBLISHER][Settings::FIELD_PUBLISHER_LOGO];

        $this->assertTrue((filter_var($logo, FILTER_VALIDATE_URL) !== false));

        //delete file
        wp_delete_file(str_replace(site_url(), WEB_SCHEMA_BASE_DIR, $logo));

        //trigger upload errors and catch them
        copy(WEB_SCHEMA_DIR . '/tests/resources/images/PublisherImage.jpg', $file);
        $_FILES[Settings::NAME]['name'][Settings::FIELD_PUBLISHER][Settings::FIELD_PUBLISHER_LOGO] = 'SettingsControllerTest';

        apply_filters('sanitize_option_' . Settings::NAME, []);
        $this->assertCount(1, get_settings_errors(Settings::NAME));

        //delete copied file
        wp_delete_file($file);

        //assert that validImageSize error is captured
        $invalidFile = WEB_SCHEMA_DIR . '/tests/resources/images/InvalidPublisherImage.jpg';
        $_FILES[Settings::NAME]['tmp_name'][Settings::FIELD_PUBLISHER][Settings::FIELD_PUBLISHER_LOGO] = $invalidFile;

        apply_filters('sanitize_option_' . Settings::NAME, []);
        $this->assertCount(2, get_settings_errors(Settings::NAME));
    }
}