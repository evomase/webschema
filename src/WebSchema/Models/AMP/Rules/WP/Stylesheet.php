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
        return preg_replace('/[\s]*[\w-]+?:[\S ]+!important;/', '', $css);
    }

    /**
     * @param $css
     * @return string
     * @link http://stackoverflow.com/questions/22312313/regex-parsing-of-css-media-queries-and-other-nested-selectors
     */
    public static function applyMediaRule($css)
    {
        return preg_replace('/[\s]*@media print[^{]+\{(?:[\s\S]+?})\s*}/', '', $css);
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

        return preg_replace('/[\s]*(?:' . $properties . '):[\S ]+;/', '', $css);
    }

    /**
     * @param string $css
     * @return string
     * @link https://www.ampproject.org/docs/guides/responsive/style_pages#disallowed-styles
     */
    public static function applyTransitionRule($css)
    {
        preg_match_all('/([\s]*)((?:-moz-|-webkit-|-o-)?transition: ?)([\S ]+);/', $css, $matches);

        foreach ($matches[3] as $i => $match) {
            $properties = explode(',', trim($match));

            foreach ($properties as $j => $property) {
                $check = explode(' ', trim($property))[0];

                if (!in_array($check, self::VALID_TRANSITION_PROPERTIES)) {
                    unset($properties[$j]);
                }
            }

            $replace = implode(', ', array_map('trim', $properties));

            if ($replace) {
                $replace = $matches[1][$i] . $matches[2][$i] . $replace . ';';
            }

            $css = preg_replace('/[\s]*' . preg_quote($matches[0][$i], '/') . '/', $replace, $css);
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

        //first remove invalid comma separated selectors
        //https://regex101.com/r/ykfIv7/3
        preg_match_all('/((?:,\n)?[\S ]*[^\/](?:' . $selectors . ')(?!\/)[\S ]*[, ])/', $css, $matches);

        foreach ($matches[0] as $match) {
            if (strpos($match, ',') !== false) {
                $css = str_replace($match, '', $css);
            }
        }

        //https://regex101.com/r/r6HvSA/6
        return preg_replace('/((?:[^}\*\/]*)(?:' . $selectors . ')(?![=\/])[^\*]+?\{[^}]+})/', '', $css);
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
            '/[\s]*\/\*[\s\S]+?\*\//s' => '', //remove comments
            '/[^}]+\{[\s]+}/'          => '', //remove all empty closures {}
            '/[\t\r\n\v\f]+|[ ]{4,}/'  => '', //remove single space character
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