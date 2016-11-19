<?php

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector;

class ShopwareRootDetector implements RootDetectorInterface
{
    /**
     * @param string $path
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
     * @return bool
     */
    private function validateDirectories($path)
    {
        foreach ($this->getShopwareDirectories() as $directory) {
            if (!is_dir($path . $directory)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function validateFiles($path)
    {
        foreach ($this->getShopwareFiles() as $file) {
            if (!file_exists($path . $file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array
     */
    private function getShopwareDirectories()
    {
        return [
            '/engine',
            '/var',
            '/bin',
            '/vendor',
            '/files'
        ];
    }

    /**
     * @return array
     */
    private function getShopwareFiles()
    {
        return [
            '/shopware.php'
        ];
    }
}
