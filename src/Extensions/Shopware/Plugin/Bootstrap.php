<?php

namespace Shopware\Plugin;

use Shopware\Plugin\Command\InstallCommand;
use Shopware\Plugin\Command\ZipCommand;
use Shopware\Plugin\Command\ZipLocalCommand;
use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This plugin will install/setup shopware in a development version
 *
 * Class Bootstrap
 * @package Shopware\Install
 */
class Bootstrap implements ContainerAwareExtension, ConsoleAwareExtension
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
            new ZipCommand(),
            new ZipLocalCommand()
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function populateContainer($container)
    {
        $container->register('plugin_column_renderer', 'Shopware\Plugin\Services\ConsoleInteraction\PluginColumnRenderer')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('config'));

        $container->register('plugin_selector', 'Shopware\Plugin\Services\ConsoleInteraction\PluginInputVerificator')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('plugin_column_renderer'));

        $container->register('plugin_operation_manager', 'Shopware\Plugin\Services\ConsoleInteraction\PluginOperationManager')
            ->addArgument(new Reference('plugin_provider'))
            ->addArgument(new Reference('plugin_selector'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('utilities'));

        $container->register('bootstrap_info', 'Shopware\Plugin\Services\BootstrapInfo');

        $container->register('install_service', 'Shopware\Plugin\Services\Install')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('io_service'));

        $container->register('zip_service', 'Shopware\Plugin\Services\Zip')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('process_executor'));

        $container->register('checkout_service', 'Shopware\Plugin\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('git_util'))
            ->addArgument(new Reference('io_service'));

        $container->register('default_repository_factory', 'Shopware\Plugin\Services\Repositories\DefaultRepositoryFactory')
            ->addArgument(new Reference('service_container'));

        $container->register('repository_manager', 'Shopware\Plugin\Services\RepositoryManager')
            ->addArgument(new Reference('extension_manager'))
            ->addArgument(new Reference('default_repository_factory'));

        $container->register('plugin_provider', 'Shopware\Plugin\Services\PluginProvider')
            ->addArgument(new Reference('config'));
    }
}
