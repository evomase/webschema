<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:35
 */

namespace WebSchema\Models\DataTypes;

class Article extends Thing
{
    const FIELD_AUTHOR = 'author';
    const FIELD_DATE_MODIFIED = 'dateModified';
    const FIELD_DATE_PUBLISHED = 'datePublished';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_HEADLINE = 'headline';
    const FIELD_IMAGE = 'image';
    const FIELD_PUBLISHER = 'publisher';

    protected $data = [
        self::FIELD_AUTHOR              => [],
        self::FIELD_DATE_MODIFIED       => null,
        self::FIELD_DATE_PUBLISHED      => null,
        self::FIELD_DESCRIPTION         => null,
        self::FIELD_HEADLINE            => null,
        self::FIELD_IMAGE               => [],
        self::FIELD_MAIN_ENTITY_OF_PAGE => null,
        self::FIELD_PUBLISHER           => []
    ];

    protected $required = [
        self::FIELD_HEADLINE,
        //self::FIELD_IMAGE, //TODO
        //self::FIELD_PUBLISHER,
        self::FIELD_DATE_PUBLISHED,
        self::FIELD_AUTHOR
    ];

    public function setImage($url)
    {
        if ($image = $this->getImage($url)) {
            return $this->setValue(self::FIELD_IMAGE, $image);
        }

        return $this;
    }

    /**
     * @param string $url
     * @return array|null
     */
    private function getImage($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) && $image = getimagesize($url) !== false) {
            $width = $image[0];

            //requirements as per Google AMP
            if ($width >= 696 && in_array($image[2], [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG])) {
                return [
                    'url'    => $url,
                    'width'  => $width,
                    'height' => $image[1]
                ];
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @param string $imageURL
     * @return $this|Article
     */
    public function setPublisher($name, $imageURL)
    {
        if ($image = $this->getImage($imageURL) && $name) {
            return $this->setValue(self::FIELD_PUBLISHER, [
                'name' => (string)$name,
                'logo' => $image
            ]);
        }

        return $this;
    }

    /**
     * @param \DateTime $dateTime
     * @return Article
     */
    public function setDatePublished(\DateTime $dateTime)
    {
        return $this->setValue(self::FIELD_DATE_PUBLISHED, $dateTime->format('c'));
    }

    /**
     * @param string $description
     * @return Article
     */
    public function setDescription($description)
    {
        return $this->setValue(self::FIELD_DESCRIPTION, $description);
    }

    /**
     * @param string $headline
     * @return Article
     */
    public function setHeadline($headline)
    {
        return $this->setValue(self::FIELD_HEADLINE, $headline);
    }

    protected function fill()
    {
        $timezone = new \DateTimeZone(date_default_timezone_get());

        $this->setAuthor(get_userdata($this->post->post_author)->display_name)
            ->setDateModified(new \DateTime($this->post->post_modified, $timezone))
            ->setDatePublished(new \DateTime($this->post->post_date, $timezone))
            ->setDescription(get_the_excerpt($this->post))
            ->setHeadline($this->post->post_title)
            ->setMainEntityOfPage(get_post_permalink($this->post->ID));
    }

    /**
     * @param \DateTime $dateTime
     * @return Article
     */
    public function setDateModified(\DateTime $dateTime)
    {
        return $this->setValue(self::FIELD_DATE_MODIFIED, $dateTime->format('c'));
    }

    /**
     * @param string $name
     * @return Article
     */
    public function setAuthor($name)
    {
        return $this->setValue(self::FIELD_AUTHOR, $name);
    }
}