<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreater;

use Shopware\PluginCreator\Services\TemplateFileProvider\LegacyOptionFileProviderLoader;

class LegacyOptionFileProviderLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLegacyLoad()
    {
        $expectedProviderAmount = 11;
        $isLegacy = true;
        $loader = new LegacyOptionFileProviderLoader($isLegacy);

        $result = $loader->load();

        $this->assertEquals($expectedProviderAmount, count($result));
    }

    public function testCurrentLoad()
    {
        $expectedProviderAmount = 11;
        $isLegacy = false;
        $loader = new LegacyOptionFileProviderLoader($isLegacy);

        $result = $loader->load();
        $this->assertEquals($expectedProviderAmount, count($result));
    }
}
