<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 29/05/2017
 * Time: 13:40
 */

namespace WebSchema\Models\WP\MetaBoxes;

use WebSchema\Models\StructuredData\StructuredData;
use WebSchema\Models\StructuredData\Types\Model;
use WebSchema\Models\WP\MetaBoxes\Interfaces\MetaBoxInterface;
use WebSchema\Models\WP\Post;

class DataType implements MetaBoxInterface
{
    const KEY = 'web-schema';
    const PARENT_TITLE = 'Web Schema';
    const TITLE = 'Data Type';

    /**
     * @var Post
     */
    private $postTypeClass;

    /**
     * @param array  $postTypes
     * @param string $postTypeClass
     */
    public function addMetaBox(array $postTypes, $postTypeClass)
    {
        $this->postTypeClass = $postTypeClass;
        MetaBox::register(self::KEY, self::PARENT_TITLE, $this, $postTypes);
    }

    /**
     * @param \WP_Post $post
     */
    public function renderMetaBox(\WP_Post $post)
    {
        $class = $this->postTypeClass;
        $post = $class::get($post->ID);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $types = StructuredData::getTypes();

        foreach ($types as $id => $class) {
            /**
             * @var Model $class
             */

            $types[$id] = $class::getSchema()->toArray();
        }

        if ($post) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $data = $post->getData();
            $data[Post::FIELD_JSON_LD] = $post->getPrettyJSON();
        } else {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $data = [];
        }

        include WEB_SCHEMA_DIR . '/resources/templates/meta-boxes/data-type.tpl.php';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return self::TITLE;
    }
}