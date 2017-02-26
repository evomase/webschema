<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:21
 */

namespace WebSchema\Models\AMP\Interfaces;

interface Rule
{
    public function __construct(\DOMDocument $document);

    public function parse();
}