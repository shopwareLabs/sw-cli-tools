<?php

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector;

class ShopwareRootDetector implements RootDetectorInterface
{
    const DIRECTORIES = [
        '/engine',
        '/var',
        '/bin',
        '/vendor',
        '/files'
    ];

    const FILES = [
        '/shopware.php'
    ];

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isRoot($path)
    {
        if ($this->validateDirectories($path) && $this->validateFiles($path)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function validateDirectories($path)
    {
        foreach (self::DIRECTORIES as $directory) {
            if (!is_dir($path.$directory)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function validateFiles($path)
    {
        foreach (self::FILES as $file) {
            if (!file_exists($path.$file)) {
                return false;
            }
        }
        return true;
    }
}
