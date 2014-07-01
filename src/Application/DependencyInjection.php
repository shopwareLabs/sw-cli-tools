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

        $container->register('process_executor', 'ShopwareCli\Services\ProcessExecutor')
            ->addArgument(new Reference('output_interface'));

        $container->register('git_identity_environment', 'ShopwareCli\Services\GitIdentityEnvironment')
            ->addArgument(new Reference('path_provider'))
            ->addArgument(new Reference('config'));

        $container->register('git_util', 'ShopwareCli\Services\GitUtil')
                ->addArgument(new Reference('output_interface'))
                ->addArgument(new Reference('git_identity_environment'));

        $container->register('utilities', 'ShopwareCli\Utilities')
            ->addArgument(new Reference('io_service'));

        $container->register('xdg', '\XdgBaseDir\Xdg');

        $container->register('directory_gateway', 'ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway')
            ->addArgument(new Reference('xdg'));

        $container->register('file_downloader', 'ShopwareCli\Services\StreamFileDownloader')
                ->addArgument(new Reference('io_service'));

        $container->register('path_provider', 'ShopwareCli\Services\PathProvider\PathProvider')
            ->addArgument(new Reference('directory_gateway'));

        $container->register('cache', 'ShopwareCli\Cache\File')
            ->addArgument($container->get('path_provider'));

        $container->register('rest_service_factory', 'ShopwareCli\Services\Rest\RestServiceFactory')
            ->addArgument(new Reference('service_container'));

        $container->register('config', 'ShopwareCli\Config')
            ->addArgument(new Reference('path_provider'));

        $container->register('extension_manager', 'ShopwareCli\Application\ExtensionManager')
            ->addArgument(new Reference('autoloader'));

        $container->register('command_manager', 'ShopwareCli\Application\CommandManager')
            ->addArgument(new Reference('extension_manager'))
            ->addArgument(new Reference('service_container'));

        return $container;
    }
}
