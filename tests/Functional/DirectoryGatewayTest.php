<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Functional;

use PHPUnit\Framework\TestCase;
use ShopwareCli\Services\PathProvider\DirectoryGateway\CliToolGateway;
use ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway;
use XdgBaseDir\Xdg;

class DirectoryGatewayTest extends TestCase
{
    public function testCliToolGateway(): void
    {
        $gateway = new CliToolGateway('/some/dir/');

        static::assertEquals('/some/dir/extensions', $gateway->getExtensionDir());
        static::assertEquals('/some/dir/assets', $gateway->getAssetsDir());
        static::assertEquals('/some/dir/cache', $gateway->getCacheDir());
        static::assertEquals('/some/dir', $gateway->getConfigDir());
    }

    public function testXdgGateway(): void
    {
        putenv('HOME=/tmp/');
        putenv('XDG_DATA_HOME=/tmp/xdg-data');
        putenv('XDG_CONFIG_HOME=/tmp/xdg-config');
        putenv('XDG_CACHE_HOME=/tmp/xdg-cache');

        $gateway = new XdgGateway(new Xdg());

        static::assertEquals('/tmp/xdg-config/sw-cli-tools/extensions', $gateway->getExtensionDir());
        static::assertEquals('/tmp/xdg-data/sw-cli-tools/assets', $gateway->getAssetsDir());
        static::assertEquals('/tmp/xdg-cache/sw-cli-tools', $gateway->getCacheDir());
        static::assertEquals('/tmp/xdg-config/sw-cli-tools', $gateway->getConfigDir());
    }
}
