<?php

namespace ShopwareCli\Command\Helpers;

use ShopwareCli\Plugin\RepositoryFactory;
use ShopwareCli\Struct\Plugin;
use ShopwareCli\Utilities;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles operations on plugins. You can either search for a single plugin, force the use to select one if multiple
 * plugins where found and then operate on that plugin (searchAndOperate) or run the plugin prompt until the user
 * selects 'all' or 'exit' (operationLoop).
 *
 * Works via callback you can pass in order to get notified about the selected plugins
 *
 * Class PluginOperationManager
 * @package ShopwareCli\Command\Helpers
 */
class PluginOperationManager
{
    protected $pluginManager;
    protected $dialog;
    protected $output;
    protected $pluginSelector;

    public function __construct(RepositoryFactory $pluginManager, PluginInputVerificator $pluginSelector, DialogHelper $dialog, OutputInterface $output, $utilities)
    {
        $this->pluginManager = $pluginManager;
        $this->pluginSelector = $pluginSelector;
        $this->dialog = $dialog;
        $this->output = $output;
        $this->utilities = $utilities;
    }

    public function searchAndOperate($names, $callback, $params)
    {
        foreach ($names as $name) {
            $plugins = $this->pluginManager->getPluginByName($name);
            $count = count($plugins);
            if ($count == 1) {
                $this->output->writeln("\nWill now process '<comment>{$name}</comment>'");
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
                $this->output->writeln("\n<error>Could not find a plugin named '{$name}'</error>");
            }
        }

    }

    public function operationLoop($callback, $params)
    {
        $plugins = $this->pluginManager->getPlugins();
        while (true) {
            $response = $this->pluginSelector->selectPlugin($plugins, array('all', 'exit'));
            if ($response == 'exit') {
                return;
            }
            if ($response instanceof \ShopwareCli\Struct\Plugin) {
                $this->utilities->cls();
                $callback($response, $params);
                $this->dialog->ask($this->output, "\n<error>Done, hit enter to continue.</error>");
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
