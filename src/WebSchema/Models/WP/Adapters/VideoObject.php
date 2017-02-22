<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 14:20
 */

namespace WebSchema\Models\WP\Adapters;

use WebSchema\Models\DataTypes\Interfaces\VideoObjectAdapter;
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

    public function getThumbnailURL()
    {
        return $this->getImageURL();
    }
}