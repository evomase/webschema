<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 14:20
 */

namespace WebSchema\Models\WP\Adapters;

use WebSchema\Models\StructuredData\Types\Interfaces\VideoObjectAdapter;
use WebSchema\Models\WP\Adapters\Traits\HasPublisher;

class VideoObject extends Model implements VideoObjectAdapter
{
    use HasPublisher;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->post->post_title;
    }

    /**
     * @return null|string
     */
    public function getThumbnailURL()
    {
        return $this->getImageURL();
    }

    /**
     * @return \DateTime
     */
    public function getUploadDate()
    {
        return new \DateTime($this->post->post_date, new \DateTimeZone(date_default_timezone_get()));
    }
}