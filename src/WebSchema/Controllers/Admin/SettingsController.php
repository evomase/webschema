<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 13:12
 */

namespace WebSchema\Controllers\Admin;

use WebSchema\Controllers\Controller;
use WebSchema\Models\DataTypes\Model as DataType;
use WebSchema\Models\WP\Settings;

class SettingsController extends Controller
{
    const SLUG = 'web-schema';

    protected static $instance;

    protected function __construct()
    {
        add_action('admin_menu', [$this, 'addMenus']);

        //This needs to be called before Settings default sanitize method
        add_filter('sanitize_option_' . Settings::NAME, [$this, 'sanitize'], 1);
    }

    public function addMenus()
    {
        add_options_page('Web Schema Settings', 'Web Schema', 'manage_options', self::SLUG, [$this, 'get']);
    }

    public function get()
    {
        include WEB_SCHEMA_DIR . '/resources/templates/admin/settings.tpl.php';
    }

    /**
     * @param array $data
     * @return array
     */
    public function sanitize(array $data)
    {
        if (!empty($_FILES[Settings::NAME])) {
            $image = $_FILES[Settings::NAME];

            foreach (array_keys($image) as $item) {
                $image[$item] = $image[$item][Settings::FIELD_PUBLISHER][Settings::FIELD_PUBLISHER_LOGO];
            }

            if (!$image['error']) {
                $size = getimagesize($image['tmp_name']);

                if (DataType::isImageValid($size)) {
                    $image = wp_handle_upload($image, ['action' => 'update']);

                    if (empty($image['error'])) {
                        $data[Settings::FIELD_PUBLISHER][Settings::FIELD_PUBLISHER_LOGO] = $image['url'];
                    } else {
                        add_settings_error(Settings::NAME, 1, $image['error']);
                    }
                } else {
                    add_settings_error(Settings::NAME, 1,
                        'Uploaded file is not valid, only JPEG,PNG, or GIF allowed and' .
                        ' a minimum width of ' . WEB_SCHEMA_AMP_IMAGE_MIN_WIDTH . ' pixels');
                }
            }
        }

        return $data;
    }
}