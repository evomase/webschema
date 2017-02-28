<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 28/02/2017
 * Time: 10:23
 */

namespace WebSchema\Models\AMP\Rules\WP;

use WebSchema\Models\AMP\Rules\Model;

class Theme extends Model
{
    const CSS_SIZE_LIMIT = 5000; //bytes

    private $stylePath = STYLESHEETPATH . '/style.css';
    private $css;

    public function parse()
    {
        $this->css = self::cleanCSS(file_get_contents($this->stylePath));
        $this->addCSS();
    }

    /**
     * @param string $css
     * @return string
     */
    public static function cleanCSS($css)
    {
        $css = preg_replace(['/\/\*.+?\*\//s', '/[\s]+/'], '', $css);
        return $css;
    }

    private function addCSS()
    {
        if (strlen($this->css) <= self::CSS_SIZE_LIMIT) {
            $style = $this->document->createElement('style');
            $style->setAttribute('amp-custom', '');
            $style->textContent = $this->css;

            $this->document->getElementsByTagName('head')->item(0)->appendChild($style);
        }
    }
}