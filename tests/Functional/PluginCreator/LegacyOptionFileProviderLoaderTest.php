<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional\PluginCreator;

use PHPUnit\Framework\TestCase;
use Shopware\PluginCreator\Services\TemplateFileProvider\LegacyOptionFileProviderLoader;

class LegacyOptionFileProviderLoaderTest extends TestCase
{
    public function testLegacyLoad(): void
    {
        $expectedProviderAmount = 11;
        $isLegacy = true;
        $result = (new LegacyOptionFileProviderLoader($isLegacy))->load();

        static::assertCount($expectedProviderAmount, $result);
    }

    public function testCurrentLoad(): void
    {
        $expectedProviderAmount = 11;
        $isLegacy = false;
        $result = (new LegacyOptionFileProviderLoader($isLegacy))->load();
        static::assertCount($expectedProviderAmount, $result);
    }
}
