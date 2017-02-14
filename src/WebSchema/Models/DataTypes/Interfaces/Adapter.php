<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 12:05
 */

namespace WebSchema\Models\DataTypes\Interfaces;

interface Adapter
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
    public function getAuthor();

    /**
     * @return string
     */
    public function getDescription();
}