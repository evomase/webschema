<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 28/02/2017
 * Time: 10:23
 */

namespace WebSchema\Models\AMP\Rules\WP;

use WebSchema\Models\AMP\Rules\Model;

class Stylesheet extends Model
{
    const CACHE_EXPIRY = WEB_SCHEMA_AMP_STYLESHEET_EXPIRY;
    const CACHE_KEY = 'web-schema-amp-stylesheet';

    const CSS_SIZE_LIMIT = 60000; //50K

    const INVALID_PROPERTIES = [
        'behavior',
        '-moz-binding',
        'filter',
        '-webkit-filter'
    ];

    const INVALID_SELECTORS = [
        '*',
        ':not',
        '.-amp-',
        '.i-amp-'
    ];
    const VALID_TRANSITION_PROPERTIES = [
        'opacity',
        'transform',
        '-webkit-transform',
        '-moz-transform',
        '-o-transform'
    ];

    private static $rules = [
        'applyImportantRule',
        'applyMediaRule',
        'applySelectorRule',
        'applyPropertyRule',
        'applyTransitionRule'
    ];

    private $path = STYLESHEETPATH . '/style.css';
    private $css;

    /**
     * @param $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applyImportantRule($css)
    {
        return preg_replace('/[\w-]+?:[\S ]+!important;/', '', $css);
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
     * @param string $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applyPropertyRule($css)
    {
        $properties = self::INVALID_PROPERTIES;

        foreach ($properties as $index => $property) {
            $properties[$index] = preg_quote($property, '/');
        }

        $properties = implode('|', $properties);

        return preg_replace('/(?:' . $properties . '):[\S ]+;/', '', $css);
    }

    /**
     * @param string $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applyTransitionRule($css)
    {
        preg_match_all('/(?:-moz-|-webkit-|-o-)?transition: ?([\S]+)[\S ]*;/', $css, $matches);

        foreach ($matches[1] as $index => $match) {
            if (!in_array($match, self::VALID_TRANSITION_PROPERTIES)) {
                $css = str_replace($matches[0][$index], '', $css);
            }
        }

        return $css;
    }

    /**
     * @param string $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applySelectorRule($css)
    {
        $selectors = self::INVALID_SELECTORS;

        foreach ($selectors as $index => $selector) {
            $selectors[$index] = preg_quote($selector, '/');
        }

        $selectors = implode('|', $selectors);

        //remove all wildcard separated selectors
        //https://regex101.com/r/ykfIv7/3
        preg_match_all('/((?:,\n)?[\S ]*[^\/](?:' . $selectors . ')(?!\/)[\S ]*[, ])/', $css, $matches);

        foreach ($matches[0] as $match) {
            if (strpos($match, ',') !== false) {
                $css = str_replace($match, '', $css);
            }
        }

        //https://regex101.com/r/r6HvSA/5
        return preg_replace('/((?:[\S ]*)(?:' . $selectors . ')(?![=\/])[^\*]+?\{[^}]+})/', '', $css);
    }

    public function parse()
    {
        $cache = false;

        if (!($this->css = get_transient(self::CACHE_KEY)) || !self::CACHE_EXPIRY) {
            $this->css = self::cleanCSS(file_get_contents($this->path));
            $cache = true;
        }

        if (self::CACHE_EXPIRY && $cache) {
            set_transient(self::CACHE_KEY, $this->css, self::CACHE_EXPIRY);
        }

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
            '/\/\*[\s\S]+?\*\//s' => '', //remove comments
            '/[^}]+\{[\s]+}/'     => '', //remove all empty closures {}
            '/[\t\r\n\v\f]+/'     => '', //remove single space character
            '/(:) | ({)/'         => '$1$2', //remove spaces between : and {
        ];

        $css = preg_replace(array_keys($regex), array_values($regex), $css);

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