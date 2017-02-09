<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:35
 */

namespace WebSchema\Models\Types;

class Article extends Model
{
    const FIELD_AUTHOR = 'author';
    const FIELD_DATE_MODIFIED = 'dateModified';
    const FIELD_DATE_PUBLISHED = 'datePublished';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_HEADLINE = 'headline';
    const FIELD_IMAGE = 'image';
    const FIELD_MAIN_ENTITY_OF_PAGE = 'mainEntityOfPage';
    const FIELD_PUBLISHER = 'publisher';

    private $data = [
        self::FIELD_AUTHOR              => [],
        self::FIELD_DATE_MODIFIED       => null,
        self::FIELD_DATE_PUBLISHED      => null,
        self::FIELD_DESCRIPTION         => null,
        self::FIELD_HEADLINE            => null,
        self::FIELD_IMAGE               => [],
        self::FIELD_MAIN_ENTITY_OF_PAGE => null,
        self::FIELD_PUBLISHER           => []
    ];

    private $required = [
        self::FIELD_HEADLINE,
        self::FIELD_IMAGE,
        self::FIELD_PUBLISHER,
        self::FIELD_DATE_PUBLISHED,
        self::FIELD_AUTHOR
    ];

    public function generateJSON()
    {
        return '';
    }

    public function setImage($url)
    {
        if ($image = $this->getImage($url)) {
            return $this->setValue(self::FIELD_IMAGE, $image);
        }

        return $this;
    }

    private function getImage($url)
    {
        if ($image = getimagesize($url) !== false) {
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

        return [];
    }

    private function setValue($key, $value)
    {
        if ($value && $key) {
            $this->data[$key] = $value;
        }

        return $this;
    }

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

    public function setDatePublished(\DateTime $dateTime)
    {
        return $this->setValue(self::FIELD_DATE_PUBLISHED, $dateTime->format('c'));
    }

    public function setDateModified(\DateTime $dateTime)
    {
        return $this->setValue(self::FIELD_DATE_MODIFIED, $dateTime->format('c'));
    }

    public function setAuthor($name)
    {
        return $this->setValue(self::FIELD_AUTHOR, $name);
    }

    public function setDescription($description)
    {
        return $this->setValue(self::FIELD_DESCRIPTION, $description);
    }

    public function setHeadline($headline)
    {
        return $this->setValue(self::FIELD_HEADLINE, $headline);
    }

    public function setMainEntityOfPage($url)
    {
        return $this->setValue(self::FIELD_MAIN_ENTITY_OF_PAGE, $url);
    }
}