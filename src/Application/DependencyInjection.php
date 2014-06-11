<?php

namespace ShopwareCli\Application;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DependencyInjection
{
    protected static $container;
    public static $basePath;

    /**
     * @return ContainerBuilder
     */
    public static function getContainer()
    {
        if (!self::$container) {
            self::createContainer();
        }
        return self::$container;
    }

    public static function createContainer()
    {
        self::$container = new ContainerBuilder();

        self::$container->register('utilities', 'ShopwareCli\Utilities');

        self::$container->register('xdg', 'ShopwareCli\Services\PathProvider\DirectoryGateway\Xdg');

        self::$container->register('directory_gateway', 'ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway')
            ->addArgument(new Reference('xdg'));

        self::$container->register('path_provider', 'ShopwareCli\Services\PathProvider\PathProvider')
            ->addArgument(new Reference('directory_gateway'));

        self::$container->setDefinition('output_writer', new Definition())->setSynthetic(true);

        self::$container->register('install_service', 'ShopwareCli\Services\Install')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('output_writer'));

        self::$container->register('zip_service', 'ShopwareCli\Services\Zip')
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('output_writer'));

        self::$container->register('checkout_service', 'ShopwareCli\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('output_writer'));

        self::$container->register('manager_factory', 'ShopwareCli\Plugin\RepositoryFactory')
            ->addArgument(new Reference('service_container'));

        self::$container->register('cache', 'ShopwareCli\Cache\File')
            ->addArgument(self::$container->get('path_provider')->getCachePath() . DIRECTORY_SEPARATOR);

        self::$container->register('rest_service_factory', 'ShopwareCli\Services\Rest\RestServiceFactory')
            ->addArgument(new Reference('service_container'));

        self::$container->register('config', 'ShopwareCli\Config')
            ->addArgument(self::$container->get('path_provider'));

        self::$container->register('plugin_manager', 'ShopwareCli\Application\PluginManager')
        ->addArgument(array(self::$container->get('path_provider')->getPluginPath(), self::$container->get('path_provider')->getCliToolPath() . '/plugins'))
        ->addArgument(new Reference('service_container'));

        self::$container->register('logger', 'ShopwareCli\Application\Logger')
            ->addArgument(new Reference('output_writer'));

        self::$container->register('command_manager', 'ShopwareCli\Application\CommandManager')
            ->addArgument(new Reference('service_container'));
    }
}