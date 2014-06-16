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
     * @var ContainerBuilder
     */
    protected $container;

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;

        $this->populateContainer();
    }

    /**
     * Return an array with instances of your console commands here
     *
     * @return mixed
     */
    public function getConsoleCommands()
    {
        return array(
            new InstallCommand(),
            new ZipCommand()
        );
    }

    private function populateContainer()
    {

        $this->container->register('plugin_column_renderer', 'Shopware\Plugin\Services\PluginColumnRenderer')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('config'));

        $this->container->register('plugin_selector', 'Shopware\Plugin\Services\PluginInputVerificator')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('plugin_column_renderer'));

        $this->container->register('plugin_operation_manager', 'Shopware\Plugin\Services\PluginOperationManager')
            ->addArgument(new Reference('plugin_provider'))
            ->addArgument(new Reference('plugin_selector'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('utilities'));


        $this->container->register('install_service', 'Shopware\Plugin\Services\Install')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('io_service'));

        $this->container->register('zip_service', 'Shopware\Plugin\Services\Zip')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'));

        $this->container->register('checkout_service', 'Shopware\Plugin\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'));

    }
}
