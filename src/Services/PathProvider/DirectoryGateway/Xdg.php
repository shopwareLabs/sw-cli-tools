<?php

namespace ShopwareCli\Services\PathProvider\DirectoryGateway;

/**
 * Simple implementation of the XDG standard http://standards.freedesktop.org/basedir-spec/basedir-spec-latest.html
 *
 * Based on the python implementation https://github.com/takluyver/pyxdg/blob/master/xdg/BaseDirectory.py
 *
 * todo:
 *  - XDG_RUNTIME_DIR is not implemented, yet
 *
 * Class Xdg
 * @package ShopwareCli\Application
 */
class Xdg
{
    /**
     * @return string
     */
    public function getHomeDir()
    {
        return getenv('HOME');
    }

    /**
     * @return string
     */
    public function getHomeConfigDir()
    {
        return getenv('XDG_CONFIG_HOME') ?: $this->getHomeDir() . DIRECTORY_SEPARATOR . '.config';
    }

    /**
     * @return string
     */
    public function getHomeDataDir()
    {
        return getenv('XDG_DATA_HOME') ?: $this->getHomeDir() . DIRECTORY_SEPARATOR . '.local' . DIRECTORY_SEPARATOR . 'share';
    }

    /**
     * @return array
     */
    public function getConfigDirs()
    {
        $configDirs = getenv('XDG_CONFIG_DIRS') ?  explode(':', getenv('XDG_CONFIG_DIRS')) : array('/etc/xdg');

        return array_merge(array($this->getHomeConfigDir()), $configDirs);
    }

    /**
     * @return array
     */
    public function getDataDirs()
    {
        $dataDirs = getenv('XDG_DATA_DIRS') ?  explode(':', getenv('XDG_DATA_DIRS')) : array('/usr/local/share', '/usr/share');

        return array_merge(array($this->getHomeDataDir()), $dataDirs);
    }

    /**
     * @return string
     */
    public function getHomeCacheDir()
    {
        return getenv('XDG_CACHE_HOME') ?: $this->getHomeDir() . DIRECTORY_SEPARATOR . '.cache';

    }
}
