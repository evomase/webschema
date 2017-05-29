<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 29/05/2017
 * Time: 16:36
 */

namespace WebSchema\Models\WP\MetaBoxes;

use WebSchema\Models\WP\MetaBoxes\Interfaces\MetaBoxInterface;

class MetaBox
{
    /**
     * @var MetaBoxInterface[]
     */
    private static $boxes;

    public static function register($key, $title, MetaBoxInterface $box, array $postTypes)
    {
        if (empty(self::$boxes[$key])) {
            add_meta_box($key, $title, function (\WP_Post $post) use ($key) {
                self::render($key, $post);
            }, $postTypes, 'advanced', 'high');
        }

        self::$boxes[$key][$box->getTitle()] = $box;
    }

    /**
     * @param string   $key
     * @param \WP_Post $post
     */
    private static function render($key, \WP_Post $post)
    {
        $boxes = [];

        foreach (self::$boxes[$key] as $title => $box) {
            /**
             * @var MetaBoxInterface $box
             */
            ob_start();
            $box->renderMetaBox($post);

            $id = strtolower(preg_replace(array('/[^0-9a-z]+/i', '/\-+/'), '-', $title));

            $boxes[$id] = [
                'title'   => $title,
                'content' => ob_get_clean()
            ];
        }

        include WEB_SCHEMA_DIR . '/resources/templates/meta-box.tpl.php';
    }
}