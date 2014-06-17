<?php

namespace Shopware\Plugin\Services;

use ShopwareCli\Services\IoService;
use ShopwareCli\Struct\Plugin;

/**
 * Handles operations on plugins. You can either search for a single plugin, force the user to select one if multiple
 * plugins where found and then operate on that plugin (searchAndOperate) or run the plugin prompt until the user
 * selects 'all' or 'exit' (operationLoop).
 *
 * Works via callback you can pass in order to get notified about the selected plugins
 *
 * Class PluginOperationManager
 * @package ShopwareCli\Command\Services
 */
class PluginOperationManager
{
    protected $pluginProvider;
    protected $output;
    protected $pluginSelector;
    /**
     * @var \ShopwareCli\Services\IoService
     */
    private $ioService;

    public function __construct(PluginProvider $pluginProvider, PluginInputVerificator $pluginSelector, IoService $ioService, $utilities)
    {
        $this->pluginProvider = $pluginProvider;
        $this->pluginSelector = $pluginSelector;
        $this->utilities = $utilities;
        $this->ioService = $ioService;
    }

    /**
     * Search the plugin provider by $name and operate on the matching plugin. If multiple plugins are found
     * the users is asked for a selection. If no plugin was found, a corresponding message is printed
     *
     * @param $names
     * @param $callback
     * @param $params
     */
    public function searchAndOperate($names, $callback, $params)
    {
        foreach ($names as $name) {
            $plugins = $this->pluginProvider->getPluginByName($name);
            $count = count($plugins);
            if ($count == 1) {
                $this->ioService->writeln("\nWill now process '<comment>{$name}</comment>'");
                $callback($plugins[0], $params);
            } elseif ($count > 1) {
                $response = $this->pluginSelector->selectPlugin($plugins, array('all'));
                if ($response instanceof Plugin) {
                    $callback($response, $params);
                } else {
                    foreach ($plugins as $plugin) {
                        $callback($plugin, $params);
                    }
                }
            } elseif ($count == 0) {
                $this->ioService->writeln("\n<error>Could not find a plugin named '{$name}'</error>");
            }
        }

    }

    /**
     * Show the plugin list to the user, until "all" or "exit" was entered
     *
     * @param $callback
     * @param $params
     */
    public function operationLoop($callback, $params)
    {
        $plugins = $this->pluginProvider->getPlugins();
        while (true) {
            $response = $this->pluginSelector->selectPlugin($plugins, array('all', 'exit'));
            if ($response == 'exit') {
                return;
            }
            if ($response instanceof \ShopwareCli\Struct\Plugin) {
                $this->utilities->cls();
                $callback($response, $params);
                $this->ioService->ask("\n<error>Done, hit enter to continue.</error>");
                $this->utilities->cls();

            } elseif ($response == 'all') {
                foreach ($plugins as $plugin) {
                    $callback($plugin, $params);
                }

                return;
            }
        }
    }
}
