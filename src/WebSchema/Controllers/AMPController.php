<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:29
 */

namespace WebSchema\Controllers;

use Masterminds\HTML5;
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
        if ($html = ob_get_clean()) {
            $document = new HTML5();
            echo (new DocumentParser($document, $html))->parse();
        }
    }
}