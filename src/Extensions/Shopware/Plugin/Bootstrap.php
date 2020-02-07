<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin;

use Shopware\Plugin\Command\InstallCommand;
use Shopware\Plugin\Command\ZipCommand;
use Shopware\Plugin\Command\ZipLocalCommand;
use Shopware\Plugin\Services\BootstrapInfo;
use Shopware\Plugin\Services\Checkout;
use Shopware\Plugin\Services\ConsoleInteraction\PluginColumnRenderer;
use Shopware\Plugin\Services\ConsoleInteraction\PluginInputVerificator;
use Shopware\Plugin\Services\ConsoleInteraction\PluginOperationManager;
use Shopware\Plugin\Services\Install;
use Shopware\Plugin\Services\PluginProvider;
use Shopware\Plugin\Services\Repositories\DefaultRepositoryFactory;
use Shopware\Plugin\Services\RepositoryManager;
use Shopware\Plugin\Services\Zip;
use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This plugin will install/setup shopware in a development version
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
        return [
            new InstallCommand(),
            new ZipCommand(),
            new ZipLocalCommand(),
        ];
    }

    /**
     * @param ContainerBuilder $container
     */
    private function populateContainer($container)
    {
        $container->register('plugin_column_renderer', PluginColumnRenderer::class)
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('config'));

        $container->register('plugin_selector', PluginInputVerificator::class)
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('plugin_column_renderer'));

        $container->register('plugin_operation_manager', PluginOperationManager::class)
            ->addArgument(new Reference('plugin_provider'))
            ->addArgument(new Reference('plugin_selector'))
            ->addArgument(new Reference('io_service'));

        $container->register('bootstrap_info', BootstrapInfo::class);

        $container->register('install_service', Install::class)
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('process_executor'));

        $container->register('zip_service', Zip::class)
            ->addArgument(new Reference('checkout_service'))
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('process_executor'));

        $container->register('checkout_service', Checkout::class)
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('git_util'))
            ->addArgument(new Reference('io_service'));

        $container->register('default_repository_factory', DefaultRepositoryFactory::class)
            ->addArgument(new Reference('service_container'));

        $container->register('repository_manager', RepositoryManager::class)
            ->addArgument(new Reference('extension_manager'))
            ->addArgument(new Reference('default_repository_factory'));

        $container->register('plugin_provider', PluginProvider::class)
            ->addArgument(new Reference('config'));
    }
}
