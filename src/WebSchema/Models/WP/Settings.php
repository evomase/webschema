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
use WebSchema\Traits\UsesHooks;

class Settings
{
    use HasData {
        fill as private;
    }
    use UsesHooks;

    const FIELD_AMP = 'amp';
    const FIELD_AMP_USE_SSL = 'use-ssl';
    const FIELD_POST_TYPES = 'post-types';

    const FIELD_PUBLISHER = 'publisher';
    const FIELD_PUBLISHER_LOGO = 'logo';
    const FIELD_PUBLISHER_NAME = 'name';

    const NAME = 'web-schema';
    const PAGE = SettingsController::SLUG;

    const SECTION_AMP = self::FIELD_AMP;
    const SECTION_POST_TYPES = self::FIELD_POST_TYPES;
    const SECTION_PUBLISHER = self::FIELD_PUBLISHER;

    private static $instance;

    private $data = [
        self::FIELD_PUBLISHER => [
            self::FIELD_PUBLISHER_NAME => '',
            self::FIELD_PUBLISHER_LOGO => ''
        ],

        self::FIELD_POST_TYPES => [],

        self::FIELD_AMP => [
            self::FIELD_AMP_USE_SSL => false
        ]
    ];

    private function __construct()
    {
        self::$instance = $this;

        $this->load();

        $this->addAction('admin_init', [$this, 'register']);
        $this->addAction('update_option_' . self::NAME, [self::class, 'boot']);
        $this->addAction('delete_option_' . self::NAME, [self::class, 'boot']);
    }

    private function load()
    {
        if (($data = get_option(self::NAME)) && is_array($data)) {
            $this->fill($data);
        }
    }

    public static function boot()
    {
        if (empty(self::$instance)) {
            new self();
        } else {
            self::$instance->load();
        }
    }

    public static function reset()
    {
        delete_option(self::NAME);
    }

    /**
     * @return Settings
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function register()
    {
        register_setting(self::NAME, self::NAME, [
            'description'       => 'Settings page to customize the Web Schema functionality',
            'default'           => $this->data,
            'sanitize_callback' => [$this, 'sanitize']
        ]);

        //Sections
        $this->addAMPSection();
        $this->addPublisherSection();
        $this->addPostTypesSection();
    }

    private function addAMPSection()
    {
        add_settings_section(self::SECTION_AMP, 'Google AMP', function () {
            echo 'Here a settings associated with how AMP works';
        }, self::PAGE);

        //settings
        add_settings_field(self::FIELD_AMP . '-use-ssl', 'Use SSL', [$this, 'renderAMPSSLField'], self::PAGE,
            self::SECTION_AMP);
    }

    private function addPublisherSection()
    {
        add_settings_section(self::SECTION_PUBLISHER, 'Publisher', function () {
            echo 'Please provide the publisher information (required by JSON-LD representation)';
        }, self::PAGE);

        add_settings_field(self::FIELD_PUBLISHER . '-name', 'Name', [$this, 'renderPublisherNameField'], self::PAGE,
            self::SECTION_PUBLISHER);

        add_settings_field(self::FIELD_PUBLISHER . '-logo', 'Logo', [$this, 'renderPublisherLogoField'], self::PAGE,
            self::SECTION_PUBLISHER);
    }

    private function addPostTypesSection()
    {
        add_settings_section(self::SECTION_POST_TYPES, 'Custom Post Types', function () {
            echo 'Select a list of custom post types that require the microdata functionality';
        }, self::PAGE);

        add_settings_field(self::FIELD_POST_TYPES, 'Post Types', [$this, 'renderPostTypesField'], self::PAGE,
            self::SECTION_POST_TYPES);
    }

    /**
     * @param array $data
     * @return array
     */
    public function sanitize(array $data)
    {
        if (get_settings_errors(self::NAME)) {
            $data = get_option(self::NAME);
        }

        //delete publisher logo if needed
        if (!empty($data[self::FIELD_PUBLISHER][self::FIELD_PUBLISHER_LOGO])
            && $data[self::FIELD_PUBLISHER][self::FIELD_PUBLISHER_LOGO] != $this->data[self::FIELD_PUBLISHER][self::FIELD_PUBLISHER_LOGO]
        ) {
            $logo = str_replace(WEB_SCHEMA_BASE_URL, ABSPATH,
                $this->data[self::FIELD_PUBLISHER][self::FIELD_PUBLISHER_LOGO]);

            wp_delete_file($logo);
        }

        //set all pre-selected post types to null - HACK >_<
        if (empty($data[self::FIELD_POST_TYPES]) && $this->data[self::FIELD_POST_TYPES]) {
            foreach ($this->data[self::FIELD_POST_TYPES] as $index => $value) {
                $data[self::FIELD_POST_TYPES][$index] = null;
            }
        }

        return array_replace_recursive($this->data, $data);
    }

    public function renderPublisherNameField()
    {
        $name = self::get(self::FIELD_PUBLISHER)[self::FIELD_PUBLISHER_NAME];
        $name = ($name) ?: get_option('blogname');

        echo '<input name="' . self::NAME . '[' . self::FIELD_PUBLISHER . '][' . self::FIELD_PUBLISHER_NAME . ']"' .
            ' value="' . $name . '" type="text" required autocomplete="off"/>';
    }

    /**
     * @param $setting
     * @return mixed|null
     */
    public static function get($setting)
    {
        if (array_key_exists($setting, self::$instance->data)) {
            return self::$instance->data[$setting];
        }

        return null;
    }

    public function renderPublisherLogoField()
    {
        $logo = self::get(self::FIELD_PUBLISHER)[self::FIELD_PUBLISHER_LOGO];

        echo '<input name="' . self::NAME . '[' . self::FIELD_PUBLISHER . '][' . self::FIELD_PUBLISHER_LOGO . ']"' .
            ' type="file" />';

        if ($logo) {
            echo '<img src="' . $logo . '" alt="image" />';
        }
    }

    public function renderAMPSSLField()
    {
        $ssl = self::get(self::FIELD_AMP)[self::FIELD_AMP_USE_SSL];

        echo '<input type="checkbox" name="' . self::NAME . '[' . self::FIELD_AMP . '][' . self::FIELD_AMP_USE_SSL . ']"' .
            checked($ssl, true, false) . ' value="1"/>';
    }

    public function renderPostTypesField()
    {
        $types = get_post_types([
            'public'   => true,
            '_builtin' => false
        ], 'objects');

        $data = self::get(self::FIELD_POST_TYPES);

        if ($types) {
            echo '<select name="' . self::NAME . '[' . self::FIELD_POST_TYPES . '][]" multiple>';

            foreach ($types as $id => $type) {
                /**
                 * @var \WP_Post_Type $type
                 */
                $selected = (in_array($id, $data)) ? 'selected="selected"' : '';

                echo '<option value="' . $id . '" ' . $selected . '>' . $type->label . '</option>';
            }
        } else {
            echo '<p>No custom post types registered</p>';
        }
    }
}