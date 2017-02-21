<?php

namespace ShopwareCli\Tests\Functional;

use ShopwareCli\Services\PathProvider\DirectoryGateway\CliToolGateway;
use ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway;
use XdgBaseDir\Xdg;

class DirectoryGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testCliToolGateway()
    {
        $gateway = new CliToolGateway('/some/dir/');

        $this->assertEquals('/some/dir/extensions', $gateway->getExtensionDir());
        $this->assertEquals('/some/dir/assets', $gateway->getAssetsDir());
        $this->assertEquals('/some/dir/cache', $gateway->getCacheDir());
        $this->assertEquals('/some/dir', $gateway->getConfigDir());
    }

    public function testXdgGateway()
    {
        putenv('HOME=/tmp/');
        putenv('XDG_DATA_HOME=/tmp/xdg-data');
        putenv('XDG_CONFIG_HOME=/tmp/xdg-config');
        putenv('XDG_CACHE_HOME=/tmp/xdg-cache');

        $gateway = new XdgGateway(new Xdg());

        $this->assertEquals('/tmp/xdg-config/sw-cli-tools/extensions', $gateway->getExtensionDir());
        $this->assertEquals('/tmp/xdg-data/sw-cli-tools/assets', $gateway->getAssetsDir());
        $this->assertEquals('/tmp/xdg-cache/sw-cli-tools', $gateway->getCacheDir());
        $this->assertEquals('/tmp/xdg-config/sw-cli-tools', $gateway->getConfigDir());
    }
}
