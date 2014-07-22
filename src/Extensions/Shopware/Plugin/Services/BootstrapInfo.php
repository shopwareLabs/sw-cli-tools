<?php

namespace Shopware\Plugin\Services;

use Shopware\Plugin\Struct\PluginBootstrap;

class BootstrapInfo
{
    /**
     * @param $bootstrapFile
     * @return PluginBootstrap
     */
    public function analyze($bootstrapFile)
    {
        $content = file_get_contents($bootstrapFile);

        list($namespace, $name) = $this->analyzeClass($content);

        $info = new PluginBootstrap();
        $info->module = $namespace;
        $info->name = $name;

        return $info;

    }

    /**
     * @param $content
     * @return array
     * @throws \RuntimeException
     */
    private function analyzeClass($content)
    {
        $pattern = '#.*Shopware_Plugins_(?P<namespace>[a-zA-Z0-9]+)_(?P<name>[a-zA-Z0-9]+)_Bootstrap.*#';
        $matches = array();
        preg_match($pattern, $content, $matches);
        if (empty($matches)) {
            throw new \RuntimeException('Could not analyze bootstrap');
        }

        return array($matches['namespace'], $matches['name']);
    }
}