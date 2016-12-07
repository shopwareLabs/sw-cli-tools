<?php

namespace Shopware\Plugin\Services\ConsoleInteraction;

use Shopware\Plugin\Struct\DisplayPlugin;
use ShopwareCli\Config;
use ShopwareCli\Services\IoService;
use Shopware\Plugin\Struct\Plugin;

/**
 * Will render a given list of plugins in a two or three column layout, add numbers and a simple legend
 *
 * Class PluginColumnRenderer
 * @package ShopwareCli\Command\Services
 */
class PluginColumnRenderer
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var bool
     */
    protected $small = false;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @param IoService $ioService
     * @param Config    $config
     */
    public function __construct(IoService $ioService, Config $config)
    {
        $this->config = $config;
        $this->ioService = $ioService;
    }

    /**
     * @param boolean $isSmall
     */
    public function setSmall($isSmall)
    {
        $this->small = $isSmall;
    }

    /**
     * Show $allPlugins formatted in columns
     *
     * @param Plugin[] $allPlugins
     */
    public function show($allPlugins)
    {
        // Output format
        if ($this->small) {
            $columns = 3;
        } else {
            $columns = 2;
        }

        $displayPlugins = $this->createDisplayPlugins($allPlugins);
        $pluginColumns  = $this->createPluginColumns($displayPlugins, $columns);

        $this->printLegend($displayPlugins);
        $this->printColumns($pluginColumns);
    }

    /**
     * @param  Plugin[]        $plugins
     * @return DisplayPlugin[]
     */
    private function createDisplayPlugins($plugins)
    {
        $displayPlugins = [];
        foreach ($plugins as $key => $plugin) {
            $displayPlugins[] = DisplayPlugin::createFromPluginAndIndex($plugin, $key + 1);
        }

        return $displayPlugins;
    }

    /**
     * @param  DisplayPlugin[] $plugins
     * @param  int             $columns
     * @return array
     */
    private function createPluginColumns($plugins, $columns)
    {
        $length = count($plugins);
        $pluginColumns = [];

        $pluginsPerColumn = ceil($length / $columns);
        // Build columns and prepare unshift plugin of each column
        for ($i = 0; $i < $columns; $i++) {
            $sliceOffset = $pluginsPerColumn * $i;
            $sliceLength = $pluginsPerColumn * ($i + 1);

            $pluginColumns[$i] = array_slice($plugins, $sliceOffset, $sliceLength);
        }

        return $pluginColumns;
    }

    /**
     * @param DisplayPlugin[][] $pluginColumns
     */
    private function printColumns($pluginColumns)
    {
        $columnCount = count($pluginColumns);
        $rowCount    = count($pluginColumns[0]) -1;

        foreach (range(0, $rowCount) as $row) {
            $currentRow = [];
            foreach (range(0, $columnCount -1) as $column) {
                if (isset($pluginColumns[$column][$row])) {
                    $currentRow[] = $pluginColumns[$column][$row];
                }
            }

            $this->printRow($currentRow);
        }
    }

    /**
     * @param DisplayPlugin[] $row
     */
    private function printRow($row)
    {
        if ($this->small) {
            $baseMask = '%4.4s #COL_START#%-25.25s#COL_END#';
            $spacer = ' ';
        } else {
            $baseMask = '%4.4s #COL_START#%-30.30s#COL_END#';
            $spacer = '                   ';
        }

        $columns = [];

        foreach ($row as $plugin) {
            $mask = $this->getMaskForPlugin($plugin, $baseMask);

            $columns[] = sprintf($mask, $plugin->index, $this->formatPlugin($plugin));
        }

        $this->ioService->write(implode($columns, $spacer));
        $this->ioService->writeln("");
    }

    /**
     * Print a little legend with the repository color keys
     *
     * @param DisplayPlugin[] $plugins
     */
    private function printLegend($plugins)
    {
        if (!$this->config['general']['enableRepositoryColors']) {
            return;
        }

        $repos = [];
        foreach ($plugins as $plugin) {
            $repos[$plugin->repoType . '(' . $plugin->repository . ')'] = $this->getColorForPlugin($plugin);
        }

        $output = [];
        foreach ($repos as $name => $color) {
            $color = $color ?: 'white';

            $output[] = "<fg={$color}>{$name}</fg={$color}>";
        }

        $this->ioService->writeln('Legend:');
        $this->ioService->writeln(implode(', ', $output) . "\n");
    }

    /**
     * Modifies the baseMask for a plugin by setting the repository colors
     *
     * @param  DisplayPlugin $plugin
     * @param  string        $baseMask
     * @return string
     */
    private function getMaskForPlugin(DisplayPlugin $plugin, $baseMask)
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

    /**
     * @param  DisplayPlugin $plugin
     * @return string
     */
    private function formatPlugin(DisplayPlugin $plugin)
    {
        return $this->formatModuleName($plugin) . '/' . $plugin->name;
    }

    /**
     * Format the module name - in "small" mode, only the first char is shown (F/B/C)
     *
     * @param  DisplayPlugin $plugin
     * @return string
     */
    private function formatModuleName(DisplayPlugin $plugin)
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
     * @param  DisplayPlugin $plugin
     * @return string
     */
    private function getColorForPlugin(DisplayPlugin $plugin)
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
