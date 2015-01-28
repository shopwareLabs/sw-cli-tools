<?php

namespace Shopware\PluginCreator\Services\IoAdapter;

/**
 * Dummy IoAdapter will collect all files in memory
 *
 * Class Dummy
 * @package Shopware\PluginCreator\Services\IoAdapter
 */
class Dummy implements IoAdapter
{
    protected $files = array();

    /**
     * @param $path
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

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }


}