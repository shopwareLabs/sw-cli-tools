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

        $container->register('utilities', 'ShopwareCli\Utilities');

        $container->register('xdg', 'ShopwareCli\Services\PathProvider\DirectoryGateway\Xdg');

        $container->register('directory_gateway', 'ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway')
            ->addArgument(new Reference('xdg'));

        $container->register('path_provider', 'ShopwareCli\Services\PathProvider\PathProvider')
            ->addArgument(new Reference('directory_gateway'));

        $container->setDefinition('output_writer', new Definition())->setSynthetic(true);

        $container->register('install_service', 'ShopwareCli\Services\Install')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('output_writer'));

        $container->register('zip_service', 'ShopwareCli\Services\Zip')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('output_writer'));

        $container->register('checkout_service', 'ShopwareCli\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('output_writer'));

        $container->register('manager_factory', 'ShopwareCli\Plugin\RepositoryFactory')
            ->addArgument(new Reference('service_container'));

        $container->register('cache', 'ShopwareCli\Cache\File')
            ->addArgument($container->get('path_provider')->getCachePath() . DIRECTORY_SEPARATOR);

        $container->register('rest_service_factory', 'ShopwareCli\Services\Rest\RestServiceFactory')
            ->addArgument(new Reference('service_container'));

        $container->register('config', 'ShopwareCli\Config')
            ->addArgument(new Reference('path_provider'));

        $container->register('plugin_manager', 'ShopwareCli\Application\PluginManager')
            ->addArgument(array($container->get('path_provider')->getPluginPath(), $container->get('path_provider')->getCliToolPath() . '/plugins'))
            ->addArgument(new Reference('service_container'));

        $container->register('logger', 'ShopwareCli\Application\Logger')
            ->addArgument(new Reference('output_writer'));

        $container->register('command_manager', 'ShopwareCli\Application\CommandManager')
            ->addArgument(new Reference('service_container'));

        return $container;
    }
}
