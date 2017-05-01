<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:17
 */

namespace WebSchema\Models\AMP;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Interfaces\Rule;
use WebSchema\Models\AMP\Rules\Attributes;
use WebSchema\Models\AMP\Rules\Document;
use WebSchema\Models\AMP\Rules\Forms;
use WebSchema\Models\AMP\Rules\Images;
use WebSchema\Models\AMP\Rules\SocialMedia\Facebook;
use WebSchema\Models\AMP\Rules\SocialMedia\Instagram;
use WebSchema\Models\AMP\Rules\SocialMedia\Pinterest;
use WebSchema\Models\AMP\Rules\SocialMedia\Twitter;
use WebSchema\Models\AMP\Rules\SocialMedia\YouTube;
use WebSchema\Models\AMP\Rules\SVG;
use WebSchema\Models\AMP\Rules\Videos;
use WebSchema\Models\AMP\Rules\WP\AdminBar;
use WebSchema\Models\AMP\Rules\WP\Stylesheet;

class DocumentParser
{
    /**
     * @var Rule[]
     */
    private static $rules = [
        Document::class, //Always first to be processed

        Images::class,
        Videos::class,
        Attributes::class,
        Forms::class,
        SVG::class,

        //Social Media
        Twitter::class,
        Instagram::class,
        Facebook::class,
        Pinterest::class,
        YouTube::class,

        //WP
        AdminBar::class,
        Stylesheet::class
    ];

    /**
     * @var \DOMDocument
     */
    private $document;
    private $html;

    /**
     * @var HTML5
     */
    private $parser;

    /**
     * DocumentParser constructor.
     * @param HTML5 $parser
     */
    public function __construct(HTML5 $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param $html
     * @return string
     */
    public function parse($html)
    {
        $this->html = $html;
        $this->document = $this->parser->loadHTML($this->html);

        foreach (self::$rules as $class) {
            /**
             * @var Rule $rule
             */
            $rule = new $class($this->document);
            $rule->parse();
        }

        return $this->parser->saveHTML($this->document);
    }
}