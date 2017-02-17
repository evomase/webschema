<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 18:50
 */

namespace WebSchema\Models\WP;

class Notify
{
    const NOTICE_ERROR = 'error';
    const NOTICE_INFO = 'info';
    const NOTICE_WARNING = 'warning';

    const SESSION_NAME = 'web-schema-notices';

    private static $notices = [
        self::NOTICE_ERROR   => [],
        self::NOTICE_WARNING => [],
        self::NOTICE_INFO    => []
    ];

    private function __construct()
    {
    }

    public static function boot()
    {
        add_action('admin_notices', [self::class, 'renderNotices']);

        if (!session_id()) {
            session_start();
        }
    }

    /**
     * @param string $message
     * @param string $type
     */
    public static function add($message, $type = self::NOTICE_INFO)
    {
        $md5 = md5($message);

        if (array_key_exists($type, self::$notices) && empty(self::$notices[$type][$md5])) {
            self::$notices[$type][$md5] = $message;
        }

        $_SESSION[self::SESSION_NAME] = self::$notices;
    }

    public static function renderNotices()
    {
        $notices = ($_SESSION[self::SESSION_NAME]) ? $_SESSION[self::SESSION_NAME] : [];

        foreach ($notices as $type => $notice) {
            if (empty($notice)) {
                continue;
            }

            $dismiss = (in_array($type, [self::NOTICE_ERROR, self::NOTICE_WARNING])) ? '' : 'is-dismissible';

            echo '<div class="notice notice-' . $type . ' ' . $dismiss . '">';

            foreach ($notice as $message) {
                echo '<p>' . $message . '</p>';
            }

            echo '</div>';
        }

        $_SESSION[self::SESSION_NAME] = self::$notices;
    }

    /**
     * @return array
     */
    public static function getNotices()
    {
        return self::$notices;
    }
}