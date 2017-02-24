<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 09/02/2017
 * Time: 16:35
 */

namespace WebSchema\Models\StructuredData\Types;

use WebSchema\Models\StructuredData\Types\Interfaces\ArticleAdapter;
use WebSchema\Models\StructuredData\Types\Traits\HasAuthor;
use WebSchema\Models\StructuredData\Types\Traits\HasPublisher;
use WebSchema\Models\Type;

class Article extends Thing
{
    use HasPublisher;
    use HasAuthor;

    const FIELD_AUTHOR = 'author';
    const FIELD_DATE_MODIFIED = 'dateModified';
    const FIELD_DATE_PUBLISHED = 'datePublished';
    const FIELD_HEADLINE = 'headline';
    const FIELD_PUBLISHER = 'publisher';
    /**
     * @var Type $schema
     */
    protected static $schema;
    protected static $name;

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
        self::FIELD_PUBLISHER,
        self::FIELD_DATE_PUBLISHED,
        self::FIELD_AUTHOR
    ];
    /**
     * @var ArticleAdapter
     */
    protected $adapter;

    /**
     * Article constructor.
     * @param ArticleAdapter $adapter
     */
    public function __construct(ArticleAdapter $adapter)
    {
        parent::__construct($adapter);
    }

    protected function fill()
    {
        $this->setAuthor($this->adapter->getAuthorName(), $this->adapter->getAuthorURL())
            ->setDateModified($this->adapter->getDateModified())
            ->setDatePublished($this->adapter->getDatePublished())
            ->setDescription($this->adapter->getDescription())
            ->setHeadline($this->adapter->getHeadline())
            ->setImage($this->adapter->getImageURL())
            ->setPublisher($this->adapter->getPublisherName(), $this->adapter->getPublisherImageURL())
            ->setMainEntityOfPage($this->adapter->getMainEntityOfPage());
    }

    /**
     * @param string $headline
     * @return $this
     */
    protected function setHeadline($headline)
    {
        return $this->setValue(static::FIELD_HEADLINE, $headline);
    }

    /**
     * @param \DateTime $dateTime
     * @return Article
     */
    protected function setDatePublished(\DateTime $dateTime)
    {
        return $this->setValue(self::FIELD_DATE_PUBLISHED, $dateTime->format('c'));
    }

    /**
     * @param \DateTime $dateTime
     * @return Article
     */
    protected function setDateModified(\DateTime $dateTime)
    {
        return $this->setValue(self::FIELD_DATE_MODIFIED, $dateTime->format('c'));
    }
}