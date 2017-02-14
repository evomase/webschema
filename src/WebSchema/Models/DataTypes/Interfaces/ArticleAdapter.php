<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 12:07
 */

namespace WebSchema\Models\DataTypes\Interfaces;

interface ArticleAdapter extends Adapter
{
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