<?php

namespace Shopware\Plugin;

use Shopware\Plugin\Command\InstallCommand;
use Shopware\Plugin\Command\ZipCommand;

use ShopwareCli\Application\ConsoleAwarePlugin;
use ShopwareCli\Application\ContainerAwarePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This plugin will install/setup shopware in a development version
 *
 * Class Bootstrap
 * @package Shopware\Install
 */
class Bootstrap implements ContainerAwarePlugin, ConsoleAwarePlugin
{
    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->populateContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleCommands()
    {
        return array(
            new InstallCommand(),
            new ZipCommand()
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function populateContainer($container)
    {
        $container->register('plugin_column_renderer', 'Shopware\Plugin\Services\PluginColumnRenderer')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('config'));

        $container->register('plugin_selector', 'Shopware\Plugin\Services\PluginInputVerificator')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('plugin_column_renderer'));

        $container->register('plugin_operation_manager', 'Shopware\Plugin\Services\PluginOperationManager')
            ->addArgument(new Reference('plugin_provider'))
            ->addArgument(new Reference('plugin_selector'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('utilities'));

        $container->register('install_service', 'Shopware\Plugin\Services\Install')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('io_service'));

        $container->register('zip_service', 'Shopware\Plugin\Services\Zip')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'));

        $container->register('checkout_service', 'Shopware\Plugin\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'));
    }
}
