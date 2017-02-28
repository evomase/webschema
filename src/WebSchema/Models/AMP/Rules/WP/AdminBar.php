<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 13:05
 */

namespace WebSchema\Models\AMP\Rules\WP;

use WebSchema\Models\AMP\Rules\Model;

class AdminBar extends Model
{
    public function parse()
    {
        $element = $this->document->getElementById('wpadminbar');

        if ($element) {
            $element->parentNode->removeChild($element);
        }
    }
}