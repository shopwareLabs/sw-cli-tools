<?php

class XdgTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return \ShopwareCli\Services\PathProvider\DirectoryGateway\Xdg
     */
    public function getXdg()
    {
        return new \ShopwareCli\Services\PathProvider\DirectoryGateway\Xdg();
    }

    public function testXdgPutCache()
    {
        putenv('XDG_DATA_HOME=tmp/');
        putenv('XDG_CONFIG_HOME=tmp/');
        putenv('XDG_CACHE_HOME=tmp/');
        $this->assertEquals('tmp/', $this->getXdg()->getHomeCacheDir());
    }

    public function testXdgPutData()
    {
        putenv('XDG_DATA_HOME=tmp/');
        $this->assertEquals('tmp/', $this->getXdg()->getHomeDataDir());
    }

    public function testXdgPutConfig()
    {
        putenv('XDG_CONFIG_HOME=tmp/');
        $this->assertEquals('tmp/', $this->getXdg()->getHomeConfigDir());
    }

    public function testXdgDataDirsShouldIncludeHomeDataDir()
    {
        putenv('XDG_DATA_HOME=tmp/');
        putenv('XDG_CONFIG_HOME=tmp/');

        $expectedDirs = array(
            'tmp/',
            '/usr/local/share',
            '/usr/share',
        );

        $this->assertEquals($expectedDirs, $this->getXdg()->getDataDirs());
    }

    public function testXdgConfigDirsShouldIncludeHomeConfigDir()
    {
        putenv('XDG_CONFIG_HOME=tmp/');

        $expectedDirs = array(
            'tmp/',
            '/etc/xdg',
        );

        $this->assertEquals($expectedDirs, $this->getXdg()->getConfigDirs());
    }

}
