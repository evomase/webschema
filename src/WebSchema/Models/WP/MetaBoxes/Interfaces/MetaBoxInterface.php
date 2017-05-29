<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 29/05/2017
 * Time: 16:20
 */

namespace WebSchema\Models\WP\MetaBoxes\Interfaces;

interface MetaBoxInterface
{
    /**
     * @param array  $postTypes
     * @param string $postTypeClass
     * @return void
     */
    public function addMetaBox(array $postTypes, $postTypeClass);

    /**
     * @param \WP_Post $post
     * @return void
     */
    public function renderMetaBox(\WP_Post $post);

    /**
     * @return string
     */
    public function getTitle();
}