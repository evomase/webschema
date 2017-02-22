<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 13:47
 */

namespace WebSchema\Models\DataTypes;

use WebSchema\Models\DataTypes\Interfaces\VideoObjectAdapter;
use WebSchema\Models\DataTypes\Traits\HasPublisher;
use WebSchema\Models\Type;

class VideoObject extends Thing
{
    use HasPublisher;

    const FIELD_PUBLISHER = 'publisher';
    const FIELD_THUMBNAIL_URL = 'thumbnailUrl';

    /**
     * @var Type $schema
     */
    protected static $schema;
    protected static $name;

    /**
     * @var VideoObjectAdapter
     */
    protected $adapter;

    protected $data = [
        self::FIELD_PUBLISHER     => [],
        self::FIELD_DESCRIPTION   => null,
        self::FIELD_THUMBNAIL_URL => [],
        self::FIELD_NAME          => null
    ];

    protected $required = [
        self::FIELD_PUBLISHER,
        self::FIELD_DESCRIPTION,
        self::FIELD_THUMBNAIL_URL,
        self::FIELD_NAME
    ];

    /**
     * VideoObject constructor.
     * @param VideoObjectAdapter $adapter
     */
    public function __construct(VideoObjectAdapter $adapter)
    {
        parent::__construct($adapter);
    }

    /**
     * Requirements as per Google AMP
     *
     * @param array $image (from getimagesize())
     * @return bool
     */
    public static function isImageValid(array $image)
    {
        return ($image[0] >= 160 && $image[0] <= 1920 && $image[1] >= 90 && $image[1] <= 1080 &&
            in_array($image[2], [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG]));
    }

    protected function fill()
    {
        $this->setThumbnailURL($this->adapter->getThumbnailURL())
            ->setDescription($this->adapter->getDescription())
            ->setPublisher($this->adapter->getPublisherName(), $this->adapter->getPublisherImageURL())
            ->setName($this->adapter->getName());
    }

    /**
     * @param string $url
     * @return $this
     */
    private function setThumbnailURL($url)
    {
        if ($image = $this->getImage($url)) {
            return $this->setValue(static::FIELD_THUMBNAIL_URL, $image['url']);
        }

        return $this;
    }
}