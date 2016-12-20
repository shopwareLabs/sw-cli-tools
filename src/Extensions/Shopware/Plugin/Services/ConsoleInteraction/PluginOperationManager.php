<?php

namespace Shopware\Plugin\Services\ConsoleInteraction;

use Shopware\Plugin\Services\PluginProvider;
use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Services\IoService;
use ShopwareCli\Utilities;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles operations on plugins.
 * You can either search for a single plugin, force the user to select one if multiple
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
    /**
     * @var PluginProvider
     */
    protected $pluginProvider;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var PluginInputVerificator
     */
    protected $pluginSelector;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @param PluginProvider         $pluginProvider
     * @param PluginInputVerificator $pluginSelector
     * @param IoService              $ioService
     * @param Utilities              $utilities
     */
    public function __construct(
        PluginProvider $pluginProvider,
        PluginInputVerificator $pluginSelector,
        IoService $ioService,
        Utilities $utilities
    ) {
        $this->pluginProvider = $pluginProvider;
        $this->pluginSelector = $pluginSelector;
        $this->utilities = $utilities;
        $this->ioService = $ioService;
    }

    /**
     * Search the plugin provider by $name and operate on the matching plugin. If multiple plugins are found
     * the users is asked for a selection. If no plugin was found, a corresponding message is printed
     *
     * @param string[] $names
     * @param callable $callback
     * @param array    $params
     */
    public function searchAndOperate($names, $callback, $params)
    {
        foreach ($names as $name) {
            $plugins = $this->pluginProvider->getPluginByName($name);
            $count = count($plugins);

            if ($count == 0) {
                $plugins = $this->pluginProvider->getPluginsByRepositoryName($name);
                $count = count($plugins);
            }
            if ($count == 0) {
                $this->ioService->writeln("\n<error>Could not find a plugin named '{$name}'</error>");

                return;
            }

            $this->ioService->writeln("\nWill now process '<comment>{$name}</comment>'");

            if ($count == 1) {
                $this->executeMethodCallback($plugins[0], $callback, $params);

                return;
            }

            $response = $this->pluginSelector->selectPlugin($plugins, ['all']);
            $plugins = $this->getPluginsFromResponse($response, $plugins);

            foreach ($plugins as $plugin) {
                $this->executeMethodCallback($plugin, $callback, $params);
            }
        }
    }

    /**
     * @param object   $subject
     * @param callable $callback
     * @param array    $params
     */
    private function executeMethodCallback($subject, $callback, $params)
    {
        call_user_func_array($callback, [$subject, &$params]);
    }

    /**
     * Prepares a response and returns an array of plugin objects
     *
     * @param $response
     * @param  Plugin[] $plugins
     * @return array
     */
    private function getPluginsFromResponse($response, $plugins)
    {
        if ($response instanceof Plugin) {
            return [$response];
        } elseif (is_array($response)) {
            return $response;
        } elseif ($response == 'all') {
            return $plugins;
        }
    }

    /**
     * Show the plugin list to the user, until "all" or "exit" was entered
     *
     * @param callable $callback
     * @param array    $params
     */
    public function operationLoop($callback, $params)
    {
        $plugins = $this->pluginProvider->getPlugins();
        while (true) {
            $response = $this->pluginSelector->selectPlugin($plugins, ['all', 'exit']);

            if ($response == 'exit') {
                return;
            }

            $this->ioService->cls();

            $responsePlugins = $this->getPluginsFromResponse($response, $plugins);
            foreach ($responsePlugins as $plugin) {
                $this->executeMethodCallback($plugin, $callback, $params);
            }
            $this->ioService->ask("\n<error>Done, hit enter to continue.</error>");
            $this->ioService->cls();
        }
    }
}
