<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Application;

use Composer\Autoload\ClassLoader;
use Shopware\PluginInfo\PluginInfo;
use ShopwareCli\Cache\File;
use ShopwareCli\Config;
use ShopwareCli\ConfigFileCollector;
use ShopwareCli\Services\GitIdentityEnvironment;
use ShopwareCli\Services\GitUtil;
use ShopwareCli\Services\IoService;
use ShopwareCli\Services\OpenSSLVerifier;
use ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway;
use ShopwareCli\Services\PathProvider\PathProvider;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Services\Rest\RestServiceFactory;
use ShopwareCli\Services\ShopwareInfo;
use ShopwareCli\Services\StreamFileDownloader;
use ShopwareCli\Utilities;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use XdgBaseDir\Xdg;

class DependencyInjection
{
    private const DEFAULT_PROCESS_TIMEOUT = 180;

    public static function createContainer($rootDir): ContainerBuilder
    {
        $container = new ContainerBuilder(
            new ParameterBag(['kernel.root_dir' => $rootDir])
        );

        // synthetic services
        $container->setDefinition('autoloader', new Definition(ClassLoader::class))->setSynthetic(true);
        $container->setDefinition('input_interface', new Definition(InputInterface::class))->setSynthetic(true);
        $container->setDefinition('output_interface', new Definition(OutputInterface::class))->setSynthetic(true);
        $container->setDefinition('question_helper', new Definition(QuestionHelper::class))->setSynthetic(true);

        $container->register('io_service', IoService::class)
            ->addArgument(new Reference('input_interface'))
            ->addArgument(new Reference('output_interface'))
            ->addArgument(new Reference('question_helper'));

        $container->register('process_executor', ProcessExecutor::class)
            ->addArgument(new Reference('output_interface'))
            ->addArgument(getenv('SW_TIMEOUT') ?: self::DEFAULT_PROCESS_TIMEOUT);

        $container->register('git_identity_environment', GitIdentityEnvironment::class)
            ->addArgument(new Reference('path_provider'))
            ->addArgument(new Reference('config'));

        $container->register('git_util', GitUtil::class)
                ->addArgument(new Reference('output_interface'))
                ->addArgument(new Reference('git_identity_environment'))
                ->addArgument(getenv('SW_TIMEOUT') ?: self::DEFAULT_PROCESS_TIMEOUT);

        $container->register('utilities', Utilities::class)
            ->addArgument(new Reference('io_service'));

        $container->register('xdg', Xdg::class);

        $container->register('plugin_info', PluginInfo::class);

        $container->register('directory_gateway', XdgGateway::class)
            ->addArgument(new Reference('xdg'));

        $container->register('file_downloader', StreamFileDownloader::class)
                ->addArgument(new Reference('io_service'));

        $container->register('path_provider', PathProvider::class)
            ->addArgument(new Reference('directory_gateway'));

        $container->register('cache', File::class)
            ->addArgument($container->get('path_provider'));

        $container->register('rest_service_factory', RestServiceFactory::class)
            ->addArgument(new Reference('service_container'));

        $container->register('config_file_collector', ConfigFileCollector::class)
            ->addArgument(new Reference('path_provider'));

        $container->register('config', Config::class)
            ->addArgument(new Reference('config_file_collector'));

        $container->register('extension_manager', ExtensionManager::class)
            ->addArgument(new Reference('autoloader'));

        $container->register('command_manager', CommandManager::class)
            ->addArgument(new Reference('extension_manager'))
            ->addArgument(new Reference('service_container'));

        $container->register('openssl_verifier', OpenSSLVerifier::class)
            ->addArgument('%kernel.root_dir%/Resources/public.key');

        $container->register('shopware_info', ShopwareInfo::class);

        return $container;
    }
}
