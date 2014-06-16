<?php

namespace ShopwareCli\Command\Helpers;

use ShopwareCli\Config;
use ShopwareCli\Services\IoService;
use ShopwareCli\Struct\Plugin;

/**
 * Will render a given list of plugins in a two or three column layout, add numbers and a simple legend
 *
 * Class PluginColumnRenderer
 * @package ShopwareCli\Command\Helpers
 */
class PluginColumnRenderer
{
    protected $config;

    protected $small = false;
    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    public function __construct(IoService $ioService, Config $config)
    {
        $this->config = $config;
        $this->ioService = $ioService;
    }

    public function setSmall($isSmall)
    {
        $this->small = $isSmall;
    }

    /**
     * Show $allPlugins formatted in columns
     *
     * @param $allPlugins
     */
    public function show($allPlugins)
    {
        $count = 1;

        $length = count($allPlugins);

        $pluginColumns = array();
        $currentPlugins = array();

        // Output format
        if ($this->small) {
            $columns = 3;
        } else {
            $columns = 2;
        }

        $offset = ceil($length / $columns);

        // Build columns and prepare unshift plugin of each column
        for ($i = 0; $i < $columns; $i++) {
            $pluginColumns[$i] = array_slice($allPlugins, $offset * $i, $offset * ($i + 1));
            $currentPlugins[$i] = array_shift($pluginColumns[$i]);
        }
        $this->printLegend($allPlugins);

        while ($currentPlugins[0]) {
            /** @var \ShopwareCli\Struct\Plugin $plugin1 */
            /** @var \ShopwareCli\Struct\Plugin $plugin2 */
            /** @var \ShopwareCli\Struct\Plugin $plugin3 */
            $plugin1 = $currentPlugins[0];
            $plugin2 = $currentPlugins[1];
            $plugin3 = isset($currentPlugins[2]) ? $currentPlugins[2] : null;

            if ($plugin1 && $plugin2 && $plugin3) {
                $this->ioService->writeln(sprintf($this->generateMaskForPlugins(array($plugin1, $plugin2, $plugin3)), $count, $this->formatPlugin($plugin1), $offset + $count, $this->formatPlugin($plugin2), $offset * 2 + $count, $this->formatPlugin($plugin3)));
            } elseif ($plugin1 && $plugin2) {
                $this->ioService->writeln(sprintf($this->generateMaskForPlugins(array($plugin1, $plugin2)), $count, $this->formatPlugin($plugin1), $offset + $count, $this->formatPlugin($plugin2), '', ''));
            } elseif ($plugin1) {
                $this->ioService->writeln(sprintf($this->generateMaskForPlugins(array($plugin1)), $count, $this->formatPlugin($plugin1), '', '', '', ''));
            } else {
                break;
            }

            foreach ($currentPlugins as $key => $plugin) {
                $currentPlugins[$key] = array_shift($pluginColumns[$key]);
            }

            $count++;
        }
    }

    /**
     * Print a little legend with the repository color keys
     *
     * @param $plugins
     */
    private function printLegend($plugins)
    {
        if (!$this->config['general']['enableRepositoryColors']) {
            return;
        }

        $repos = array();
        foreach ($plugins as $plugin) {
            $repos[$plugin->repoType . '(' . $plugin->repository . ')'] = $this->getColorForPlugin($plugin);
        }

        $output = array();
        foreach ($repos as $name => $color) {
            $color = $color ?: 'white';

            $output[] = "<fg={$color}>{$name}</fg={$color}>";
        }

        $this->ioService->writeln('Legend: ' . implode(', ', $output) . "\n");
    }

    /**
     * Returns a sprintf mask for the passed plugins
     *
     * @param $plugins
     * @return string
     */
    private function generateMaskForPlugins($plugins)
    {
        if ($this->small) {
            $baseMask = '%4.4s #COL_START#%-25.25s#COL_END#';
            $spacer = ' ';
        } else {
            $baseMask = '%4.4s #COL_START#%-30.30s#COL_END#';
            $spacer = '                   ';
        }

        $output = array();
        $iterations = $this->small ? 3 : 2;
        for ($i=0; $i<$iterations; $i++) {
            $plugin = null;
            if (isset($plugins[$i])) {
                $plugin = $plugins[$i];
            }
            $output[] = $this->getMaskForPlugin($plugin, $baseMask);
        }

        return implode($spacer, $output);

    }

    /**
     * Modifies the baseMask for a plugin by setting the repository colors
     *
     * @param $plugin
     * @param $baseMask
     * @return mixed
     */
    private function getMaskForPlugin($plugin, $baseMask)
    {
        $color = $this->getColorForPlugin($plugin);

        if (!$this->config['general']['enableRepositoryColors'] || !$plugin || !$color) {
            $baseMask = str_replace('#COL_START#', '', $baseMask);
            $baseMask = str_replace('#COL_END#', '', $baseMask);
        } else {
            $baseMask = str_replace('#COL_START#', "<fg={$color}>", $baseMask);
            $baseMask = str_replace('#COL_END#', "</fg={$color}>", $baseMask);
        }

        return $baseMask;
    }

    private function formatPlugin($plugin)
    {
        return $this->formatModuleName($plugin) . '/' . $plugin->name;
    }

    /**
     * Format the module name - in "small" mode, only the first char is shown (F/B/C)
     *
     * @param  Plugin $plugin
     * @return mixed
     */
    private function formatModuleName(Plugin $plugin)
    {
        if ($this->small) {
            return $plugin->module[0];
        } else {
            return $plugin->module;
        }
    }

    /**
     * Get the configured color for the given plugin's repository
     *
     * @param $plugin
     * @return mixed
     */
    private function getColorForPlugin($plugin)
    {
        $repos = $this->config->getRepositories();
        $hasColorConfig = $plugin ? isset($repos[$plugin->repoType]['repositories'][$plugin->repository]['color']) : false;

        if (!$hasColorConfig) {
            return false;
        }

        $color = $repos[$plugin->repoType]['repositories'][$plugin->repository]['color'];

        return $color;
    }
}
