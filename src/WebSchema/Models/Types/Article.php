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
        self::FIELD_AUTHOR              => null,
        self::FIELD_DATE_MODIFIED       => null,
        self::FIELD_DATE_PUBLISHED      => null,
        self::FIELD_DESCRIPTION         => null,
        self::FIELD_HEADLINE            => null,
        self::FIELD_IMAGE               => null,
        self::FIELD_MAIN_ENTITY_OF_PAGE => null,
        self::FIELD_PUBLISHER           => null
    ];

    private $required = [
        self::FIELD_HEADLINE,
        self::FIELD_IMAGE,
        self::FIELD_PUBLISHER,
        self::FIELD_DATE_PUBLISHED,
        self::FIELD_AUTHOR
    ];

    public function generateMicroData()
    {
        return '';
    }

    public function setImage($path)
    {
        return $this;
    }

    public function setPublisher($name, $imagePath)
    {
        return $this;
    }

    public function setDatePublished(\DateTime $dateTime)
    {
        return $this;
    }

    public function setDateModified(\DateTime $dateTime)
    {
        return $this;
    }

    public function setAuthor($name)
    {
        return $this;
    }

    public function setDescription($value)
    {
        return $this;
    }
}