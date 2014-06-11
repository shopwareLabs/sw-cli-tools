<?php

class DirectoryGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testCliToolGateway()
    {
        $gateway = new \ShopwareCli\Services\PathProvider\DirectoryGateway\CliToolGateway();

        $this->assertStringMatchesFormat('%Aplugins', $gateway->getPluginDir());
        $this->assertStringMatchesFormat('%Acache', $gateway->getCacheDir());
        $this->assertStringMatchesFormat('%Aassets', $gateway->getAssetsDir());
    }

    public function testXdgGateway()
    {
        $gateway = new \ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway(new \ShopwareCli\Services\PathProvider\DirectoryGateway\Xdg());

        $this->assertStringMatchesFormat('%Aplugins', $gateway->getPluginDir());
        $this->assertStringMatchesFormat('%A.cache/sw-cli-tools', $gateway->getCacheDir());
        $this->assertStringMatchesFormat('%Aassets', $gateway->getAssetsDir());
    }
}
