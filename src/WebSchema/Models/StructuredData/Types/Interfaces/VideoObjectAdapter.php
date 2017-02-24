<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 22/02/2017
 * Time: 14:17
 */

namespace WebSchema\Models\StructuredData\Types\Interfaces;

interface VideoObjectAdapter extends Adapter
{
    /**
     * @return string
     */
    public function getPublisherName();

    /**
     * @return string
     */
    public function getPublisherImageURL();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getThumbnailURL();

    /**
     * @return \DateTime
     */
    public function getUploadDate();
}