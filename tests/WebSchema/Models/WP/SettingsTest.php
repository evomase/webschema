<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 21/02/2017
 * Time: 18:22
 */

namespace WebSchema\Tests\Models\WP;

use WebSchema\Models\WP\Settings;
use WebSchema\Tests\AbstractTestCase;

class SettingsTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function testActions()
    {
        //has_action('admin_init', )
        $this->assertEquals(10, has_action('update_option_' . Settings::NAME, [Settings::class, 'boot']));
        $this->assertEquals(10, has_action('delete_option_' . Settings::NAME, [Settings::class, 'boot']));
    }

    public function testRegister()
    {
        global $wp_settings_fields, $wp_registered_settings;

        Settings::getInstance()->register();

        $this->assertNotEmpty($wp_registered_settings[Settings::NAME]);

        $settings = $wp_settings_fields[Settings::PAGE];
        $this->assertNotEmpty($settings);
        $this->assertNotEmpty($settings[Settings::SECTION_PUBLISHER]);
        $this->assertNotEmpty($settings[Settings::SECTION_PUBLISHER]);
    }

    public function testSanitize()
    {
        global $wp_settings_errors;

        add_settings_error(Settings::NAME, 1, 'Test Error');

        $data = Settings::getInstance()->sanitize([]);

        $this->assertNotEmpty($data);
        $this->assertInternalType('array', $data);

        unset($wp_settings_errors[0]);
    }
}