<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\IoAdapter;

/**
 * HardDrive IoAdapter actually performs IO on the hdd
 */
class HardDrive implements IoAdapter
{
    /**
     * @return bool
     */
    public function exists($path)
    {
        return \file_exists($path);
    }

    public function createDirectory($path)
    {
        if ($this->exists($path)) {
            return;
        }

        $success = \mkdir($path, 0777, true);

        if (!$success) {
            throw new \RuntimeException("Could not create »{$path}«. Check your directory permission");
        }
    }

    public function createFile($file, $content)
    {
        \file_put_contents($file, $content);
    }
}
