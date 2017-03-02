<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:29
 */

namespace WebSchema\Controllers;

use WebSchema\Models\AMP\DocumentParser;
use WebSchema\Models\AMP\Route;

class AMPController extends Controller
{
    /**
     * @var Route
     */
    private $route;

    /**
     * @var DocumentParser
     */
    private $parser;

    public function __construct(Route $route, DocumentParser $parser)
    {
        parent::__construct();

        $this->route = $route;
        $this->parser = $parser;

        $this->register();
    }

    private function register()
    {
        $this->addAction('template_redirect', $callback = function () {
            if ($this->route->isAMP()) {
                ob_start();
            }
        });

        $this->addAction('shutdown', $callback = function () {
            if ($this->route->isAMP()) {
                $this->parseHTML();
            }
        }, -1);
    }

    private function parseHTML()
    {
        if ($html = ob_get_clean()) {
            echo $this->parser->parse($html);
        }
    }
}