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
    const CSS_SIZE_LIMIT = 50000; //50K

    private static $rules = [
        'applyImportantRule',
        'applyMediaRule',
        'applyWildCardRule'
    ];

    private $stylePath = STYLESHEETPATH . '/style.css';
    private $css;

    /**
     * @param $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applyImportantRule($css)
    {
        return preg_replace('/[\w-]+?:(?: ?)[\w .%-()#]+ !important;/', '', $css);
    }

    /**
     * @param $css
     * @return string
     * @link http://stackoverflow.com/questions/22312313/regex-parsing-of-css-media-queries-and-other-nested-selectors
     */
    public static function applyMediaRule($css)
    {
        return preg_replace('/@media print[^{]+\{([\s\S]+?})\s*}/', '', $css);
    }

    /**
     * @param $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applyWildCardRule($css)
    {
        //remove all wildcard separated selectors
        preg_match_all('/((?:,\n)?[\S ]*[^\/]\*[\S ]*[, ])/', $css, $matches);

        foreach($matches[0] as $match)
        {
            if (strpos($match, ',') !== false) {
               $css = str_replace($match, '', $css);
            }
        }

        return preg_replace('/((?:[\S ]*)\*(?!=)[^}\/]+})/', '', $css);
    }

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
        foreach (self::$rules as $rule) {
            $css = self::$rule($css);
        }

        $regex = [
            '/\/\*[\s\S]+?\*\//s'  => '', //remove comments
            '/[\t\r\n\v\f]+/' => '', //remove single space character
            '/(:) | ({)/'     => '$1$2', //remove spaces between : and {
        ];

        $css = preg_replace(array_keys($regex), array_values($regex), $css);

        return $css;
    }

    private function addCSS()
    {
//        echo strlen($this->css);
//        exit;

        if (strlen($this->css) <= self::CSS_SIZE_LIMIT) {
            $style = $this->document->createElement('style');
            $style->setAttribute('amp-custom', '');
            $style->textContent = $this->css;

            $this->document->getElementsByTagName('head')->item(0)->appendChild($style);
        }
    }
}