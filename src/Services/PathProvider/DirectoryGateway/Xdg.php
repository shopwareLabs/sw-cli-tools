<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

/**
 * Simple implementation of the XDG standard based on the python implementation:
 *  https://github.com/takluyver/pyxdg/blob/master/xdg/BaseDirectory.py
 *
 * Class Xdg
 * @package ShopwareCli\Application
 */
class Xdg
{
    public function getHomeDir()
    {
        return getenv('HOME');
    }

    public function getHomeConfigDir()
    {
        return getenv('XDG_CONFIG_HOME') ?: $this->getHomeDir() . DIRECTORY_SEPARATOR . '.config';
    }

    public function getHomeDataDir()
    {
        return getenv('XDG_DATA_HOME') ?: $this->getHomeDir() . DIRECTORY_SEPARATOR . '.local' . DIRECTORY_SEPARATOR . 'share';
    }

    public function getConfigDirs()
    {
        $configDirs = getenv('XDG_CONFIG_DIRS') ?  explode(':', getenv('XDG_CONFIG_DIRS')) : array('/etc/xdg');

        return array_merge(array($this->getHomeConfigDir()), $configDirs);
    }

    public function getDataDirs()
    {
        $dataDirs = getenv('XDG_DATA_DIRS') ?  explode(':', getenv('XDG_DATA_DIRS')) : array('/usr/local/share', '/usr/share');

        return array_merge(array($this->getHomeDataDir()), $dataDirs);
    }

    public function getHomeCacheDir()
    {
        return getenv('XDG_CACHE_HOME') ?: $this->getHomeDir() . DIRECTORY_SEPARATOR . '.cache';

    }

}
