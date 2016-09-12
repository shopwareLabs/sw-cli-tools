<?php

namespace ShopwareCli\tests\PluginCreater;

use Shopware\PluginCreator\Services\TemplateFileProvider\LegacyOptionFileProviderLoader;

class FileProviderLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLegacyLoad()
    {
        $expectedProviderAmount = 10;
        $isLegacy = true;
        $loader = new LegacyOptionFileProviderLoader($isLegacy);

        $result = $loader->load();

        $this->assertEquals($expectedProviderAmount, count($result));
    }

    public function testCurrentLoad()
    {
        $expectedProviderAmount = 10;
        $isLegacy = false;

        $loader = new LegacyOptionFileProviderLoader($isLegacy);

        $result = $loader->load();
        $this->assertEquals($expectedProviderAmount, count($result));
    }
}
