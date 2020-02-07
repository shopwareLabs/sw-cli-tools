<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\IoAdapter;

/**
 * Dummy IoAdapter will collect all files in memory
 */
class Dummy implements IoAdapter
{
    protected $files = [];

    /**
     * @return bool
     */
    public function exists($path)
    {
        return false;
    }

    public function createDirectory($dir)
    {
        return true;
    }

    public function createFile($file, $content)
    {
        $this->files[$file] = $content;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
