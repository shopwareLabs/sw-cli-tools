<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DependencyInjection
{
    /**
     * @return ContainerBuilder
     */
    public static function createContainer()
    {
        $container = new ContainerBuilder();

        // synthetic services
        $container->setDefinition('autoloader', new Definition('Composer\Autoload\ClassLoader'))->setSynthetic(true);
        $container->setDefinition('input_interface', new Definition('Symfony\Component\Console\Input\InputInterface'))->setSynthetic(true);
        $container->setDefinition('output_interface', new Definition('Symfony\Component\Console\Input\InputInterface'))->setSynthetic(true);
        $container->setDefinition('question_helper', new Definition('Symfony\Component\Console\Helper\QuestionHelper'))->setSynthetic(true);

        $container->register('io_service', 'ShopwareCli\Services\IoService')
            ->addArgument(new Reference('input_interface'))
            ->addArgument(new Reference('output_interface'))
            ->addArgument(new Reference('question_helper'));

        $container->register('utilities', 'ShopwareCli\Utilities')
            ->addArgument(new Reference('io_service'));

        $container->register('xdg', 'ShopwareCli\Services\PathProvider\DirectoryGateway\Xdg');

        $container->register('directory_gateway', 'ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway')
            ->addArgument(new Reference('xdg'));

        $container->register('path_provider', 'ShopwareCli\Services\PathProvider\PathProvider')
            ->addArgument(new Reference('directory_gateway'));

        $container->register('install_service', 'ShopwareCli\Services\Install')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('io_service'));

        $container->register('zip_service', 'ShopwareCli\Services\Zip')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'));

        $container->register('checkout_service', 'ShopwareCli\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'));

        $container->register('plugin_provider', 'ShopwareCli\Plugin\PluginProvider')
            ->addArgument(new Reference('service_container'));

        $container->register('cache', 'ShopwareCli\Cache\File')
            ->addArgument($container->get('path_provider'));

        $container->register('rest_service_factory', 'ShopwareCli\Services\Rest\RestServiceFactory')
            ->addArgument(new Reference('service_container'));

        $container->register('config', 'ShopwareCli\Config')
            ->addArgument(new Reference('path_provider'));

        $container->register('plugin_manager', 'ShopwareCli\Application\PluginManager')
            ->addArgument(array($container->get('path_provider')->getPluginPath(), $container->get('path_provider')->getCliToolPath() . '/plugins'))
            ->addArgument(new Reference('autoloader'))
            ->addArgument(new Reference('service_container'));

        $container->register('command_manager', 'ShopwareCli\Application\CommandManager')
            ->addArgument(new Reference('service_container'));

        $container->register('plugin_column_renderer', 'ShopwareCli\Command\Helpers\PluginColumnRenderer')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('config'));

        $container->register('plugin_selector', 'ShopwareCli\Command\Helpers\PluginInputVerificator')
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('plugin_column_renderer'));

        $container->register('plugin_operation_manager', 'ShopwareCli\Command\Helpers\PluginOperationManager')
            ->addArgument(new Reference('plugin_provider'))
            ->addArgument(new Reference('plugin_selector'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('utilities'));

        return $container;
    }
}
