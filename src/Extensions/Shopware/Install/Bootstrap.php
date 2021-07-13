<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install;

use Shopware\Install\Command\ShopwareClearCacheCommand;
use Shopware\Install\Command\ShopwareInstallReleaseCommand;
use Shopware\Install\Command\ShopwareInstallVcsCommand;
use Shopware\Install\Services\Checkout;
use Shopware\Install\Services\ConfigWriter;
use Shopware\Install\Services\Database;
use Shopware\Install\Services\Demodata;
use Shopware\Install\Services\Install\Release;
use Shopware\Install\Services\Install\Vcs;
use Shopware\Install\Services\Owner;
use Shopware\Install\Services\PostInstall;
use Shopware\Install\Services\ReleaseDownloader;
use Shopware\Install\Services\VcsGenerator;
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
            new ShopwareInstallVcsCommand(),
            new ShopwareInstallReleaseCommand(),
            new ShopwareClearCacheCommand(),
        ];
    }

    private function populateContainer(ContainerBuilder $container)
    {
        $container->register('shopware_checkout_service', Checkout::class)
            ->addArgument(new Reference('git_util'))
            ->addArgument(new Reference('io_service'))
            ->setPublic(true);

        $container->register('post_install', PostInstall::class)
            ->addArgument(new Reference('process_executor'))
            ->addArgument(new Reference('shopware-install.owner'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware_info'))
            ->setPublic(true);

        $container->register('shopware_release_download_service', ReleaseDownloader::class)
            ->addArgument(new Reference('process_executor'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('file_downloader'))
            ->addArgument(new Reference('openssl_verifier'))
            ->addArgument($container->get('path_provider')->getCachePath())
            ->setPublic(true);

        $container->register('shopware-install.vcs_generator', VcsGenerator::class)
            ->addArgument(new Reference('io_service'))
            ->setPublic(true);

        $container->register('shopware-install.config_writer', ConfigWriter::class)
            ->addArgument(new Reference('io_service'))
            ->setPublic(true);

        $container->register('shopware-install.owner', Owner::class)
            ->setPublic(true);

        $container->register('shopware-install.database', Database::class)
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('process_executor'))
            ->setPublic(true);

        $container->register('shopware-install.demodata', Demodata::class)
            ->addArgument(new Reference('path_provider'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('shopware_info'))
            ->addArgument(new Reference('process_executor'))
            ->setPublic(true);

        $container->register('shopware_vcs_install_service', Vcs::class)
            ->addArgument(new Reference('shopware_checkout_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('post_install'))
            ->setPublic(true);

        $container->register('shopware_release_install_service', Release::class)
            ->addArgument(new Reference('shopware_release_download_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('post_install'))
            ->addArgument(new Reference('process_executor'))
            ->setPublic(true);
    }
}
