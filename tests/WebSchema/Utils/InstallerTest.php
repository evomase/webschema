<?php

/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/12/2016
 * Time: 16:32
 */

namespace WebSchema\Tests\Utils;

use WebSchema\Models\Property;
use WebSchema\Models\Type;
use WebSchema\Models\TypeProperty;
use WebSchema\Tests\AbstractTestCase;
use WebSchema\Utils\Installer;

class InstallerTest extends AbstractTestCase
{
    public function testRunOnce()
    {
        $installer = new Installer();

        $this->assertTrue($installer->disableImport()->runOnce());
    }

    public function testImport()
    {
        Property::boot();
        Type::boot();
        TypeProperty::boot();

        $installer = new Installer();

        $this->assertTrue($installer->runOnce());
    }
}