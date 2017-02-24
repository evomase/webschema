<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 12:07
 */

namespace WebSchema\Models\StructuredData\Types\Interfaces;

interface ArticleAdapter extends Adapter
{
    /**
     * @return string
     */
    public function getMainEntityOfPage();

    /**
     * @return string
     */
    public function getHeadline();

    /**
     * @return string
     */
    public function getAuthorName();

    /**
     * @return string
     */
    public function getAuthorURL();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getImageURL();

    /**
     * @return \DateTime
     */
    public function getDateModified();

    /**
     * @return \DateTime
     */
    public function getDatePublished();

    /**
     * @return string
     */
    public function getPublisherName();

    /**
     * @return string
     */
    public function getPublisherImageURL();
}