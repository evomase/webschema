<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 14:21
 */

namespace WebSchema\Models\WP\Adapters\Traits;

use WebSchema\Models\WP\Settings;

trait HasPublisher
{
    /**
     * @return string
     */
    public function getPublisherImageURL()
    {
        if ($publisher = Settings::get(Settings::FIELD_PUBLISHER)) {
            return $publisher[Settings::FIELD_PUBLISHER_LOGO];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPublisherName()
    {
        if ($publisher = Settings::get(Settings::FIELD_PUBLISHER)) {
            return $publisher[Settings::FIELD_PUBLISHER_NAME];
        }

        return null;
    }
}