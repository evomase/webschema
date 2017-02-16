<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 13:27
 */

namespace WebSchema\Models\WP;

use WebSchema\Controllers\Admin\SettingsController;
use WebSchema\Models\Traits\HasData;

class Settings
{
    use HasData;

    const FIELD_POST_TYPES = 'post-types';
    const FIELD_PUBLISHER = 'publisher';
    const FIELD_PUBLISHER_LOGO = 'logo';
    const FIELD_PUBLISHER_NAME = 'name';
    const NAME = 'web-schema';
    const PAGE = SettingsController::SLUG;

    const SECTION_POST_TYPES = self::FIELD_POST_TYPES;
    const SECTION_PUBLISHER = self::FIELD_PUBLISHER;

    private static $instance;

    private $data = [
        self::FIELD_PUBLISHER => [
            self::FIELD_PUBLISHER_NAME => '',
            self::FIELD_PUBLISHER_LOGO => ''
        ],

        self::FIELD_POST_TYPES => []
    ];

    private function __construct()
    {
        self::$instance = $this;

        $this->load();

        add_action('admin_init', [$this, 'register']);
    }

    public static function boot()
    {
        new self();
    }

    private function load()
    {

    }

    public function register()
    {
        register_setting(self::NAME, self::NAME, [
            'description' => 'Settings page to customize the Web Schema functionality',
            'default'     => $this->data
        ]);

        //Sections
        add_settings_section(self::SECTION_POST_TYPES, 'Post Type Settings', function () {
            echo 'Select a list of custom post types that require the microdata functionality';
        }, self::PAGE);

        add_settings_section(self::SECTION_PUBLISHER, 'Publisher Settings', function () {
            echo 'Please provide the publisher information (required by JSON-LD representation)';
        }, self::PAGE);

        //Publisher
        add_settings_field(self::FIELD_PUBLISHER . 'Name', 'Name', [$this, 'renderPublisherNameField'], self::PAGE,
            self::SECTION_PUBLISHER);

        add_settings_field(self::FIELD_PUBLISHER . 'Logo', 'Logo', [$this, 'renderPublisherLogoField'], self::PAGE,
            self::SECTION_PUBLISHER);

        //Post Types
        add_settings_field(self::FIELD_POST_TYPES, 'Post Types', [$this, 'renderPostTypesField'], self::PAGE,
            self::SECTION_POST_TYPES);
    }

    public function getInstance()
    {
        return self::$instance;
    }

    public function renderPublisherNameField()
    {
        $data = get_option(self::NAME)[self::FIELD_PUBLISHER][self::FIELD_PUBLISHER_NAME];
        $data = ($data) ?: get_option('blogname');

        echo '<input name="' . self::NAME . '[' . self::FIELD_PUBLISHER . '][' . self::FIELD_PUBLISHER_NAME . ']"' .
            ' value="' . $data . '" type="text" required/>';
    }

    public function renderPublisherLogoField()
    {
        $data = get_option(self::NAME)[self::FIELD_PUBLISHER][self::FIELD_PUBLISHER_LOGO];

        echo '<input name="' . self::NAME . '[' . self::FIELD_PUBLISHER . '][' . self::FIELD_PUBLISHER_LOGO . ']"' .
            ' type="file" required/>';

        if ($data) {

        }
    }

    public function renderPostTypesField()
    {
        $types = get_post_types([
            'public'   => true,
            '_builtin' => true
        ], 'objects');

        $data = get_option(self::NAME)[self::FIELD_POST_TYPES];

        echo '<select name="' . self::NAME . '[' . self::FIELD_POST_TYPES . ']" multiple>';

        foreach ($types as $id => $type) {
            /**
             * @var \WP_Post_Type $type
             */
            $selected = (in_array($id, $data)) ? 'selected="selected"' : '';

            echo '<option value="' . $id . '" ' . $selected . '>' . $type->label . '</option>';
        }
    }
}