<?php


namespace Shopware\PluginCreator\Services\IoAdapter;

/**
 * IoAdapter is a generic interface for some file operations needed in the plugin
 *
 * Interface IoAdapter
 * @package Shopware\PluginCreator\Services\IoAdapter
 */
interface IoAdapter
{
    /**
     * @param $path
     * @return bool
     */
    public function exists($path);

    /**
     * @param $dir
     * @return mixed
     */
    public function createDirectory($dir);

    /**
     * @param $file
     * @param $content
     * @return mixed
     */
    public function createFile($file, $content);
}