<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 13:48
 */

namespace WebSchema\Models\DataTypes\Traits;

use WebSchema\Utils\JsonLD\Node;

trait HasPublisher
{
    /**
     * @param string $name
     * @param string $imageURL
     * @return $this
     */
    protected function setPublisher($name, $imageURL)
    {
        if ($image = $this->getPublisherImage($imageURL)) {
            return $this->setValue(self::FIELD_PUBLISHER, [
                'name' => (string)$name,
                'logo' => new Node('ImageObject', $image)
            ]);
        }

        return $this;
    }

    /**
     * @param string $url
     * @return array
     */
    protected function getPublisherImage($url)
    {
        if (($image = $this->getImageSize($url)) && static::isValidPublisherImage($image)) {
            return [
                'url'    => $url,
                'width'  => $image[0],
                'height' => $image[1]
            ];
        }

        return null;
    }

    /**
     * @param array $image
     * @return bool
     */
    public static function isValidPublisherImage(array $image)
    {
        return ($image[0] == WEB_SCHEMA_AMP_PUBLISHER_LOGO_WIDTH && $image[1] == WEB_SCHEMA_AMP_PUBLISHER_LOGO_HEIGHT
            && $image[0] > $image[1]);
    }
}