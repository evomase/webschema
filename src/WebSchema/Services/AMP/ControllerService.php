<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 16:08
 */

namespace WebSchema\Services\AMP;

use Masterminds\HTML5;
use WebSchema\Controllers\AMPController;
use WebSchema\Models\AMP\DocumentParser;
use WebSchema\Models\AMP\Route;
use WebSchema\Services\ControllerService as BaseControllerService;

class ControllerService extends BaseControllerService
{
    public static function boot()
    {
        new AMPController(Route::getInstance(), new DocumentParser(new HTML5()));
    }

    public static function shutdown()
    {
        AMPController::shutdown();
    }
}