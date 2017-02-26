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
    protected function __construct()
    {
        add_action('template_redirect', function () {
            if (Route::isAMP()) {
                ob_start();
            }
        });

        add_action('shutdown', function () {
            if (Route::isAMP()) {
                $this->parseHTML();
            }
        }, -1);
    }

    private function parseHTML()
    {
        $html = ob_get_clean();

        libxml_use_internal_errors(true);

        $document = new \DOMDocument();
        $document->loadHTML($html);

        echo (new DocumentParser($document))->parse();
    }
}