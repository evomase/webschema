<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:32
 */

namespace tests\WebSchema\Utils;

use tests\WebSchema\AbstractTestCase;
use WebSchema\Utils\Installer;

class InstallerTest extends AbstractTestCase
{
    public function testRunOnce()
    {
        $installer = new Installer();

        $this->assertTrue($installer->runOnce());
    }
}