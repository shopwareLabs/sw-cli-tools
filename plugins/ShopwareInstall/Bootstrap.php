<?php

namespace Plugin\ShopwareInstall;

use Plugin\ShopwareInstall\Command\ShopwareInstallVcsCommand;
use Plugin\ShopwareInstall\Command\ShopwareInstallReleaseCommand;
use Plugin\ShopwareInstall\Command\ShopwareClearCacheCommand;
use ShopwareCli\Application\ConsoleAwarePlugin;
use ShopwareCli\Application\ContainerAwarePlugin;
use ShopwareCli\Application\DependencyInjection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This plugin will install/setup shopware in a development version
 *
 * Class Bootstrap
 * @package Plugin\ShopwareInstall
 */
class Bootstrap implements ContainerAwarePlugin, ConsoleAwarePlugin
{

    protected $container;

    public function __construct(ContainerBuilder $container)
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
            new ShopwareInstallVcsCommand(),
            new ShopwareInstallReleaseCommand(),
            new ShopwareClearCacheCommand()
        );
    }

    private function populateContainer()
    {
        $this->container->register('shopware_checkout_service', 'Plugin\ShopwareInstall\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('logger'));

        $this->container->register('shopware_release_download_service', 'Plugin\ShopwareInstall\Services\ReleaseDownloader')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('logger'))
            ->addArgument($this->container->get('path_provider')->getCachePath());

        $this->container->register('shopware-install.vcs_generator', 'Plugin\ShopwareInstall\Services\VcsGenerator');
        $this->container->register('shopware-install.config_writer', 'Plugin\ShopwareInstall\Services\ConfigWriter');
        $this->container->register('shopware-install.database', 'Plugin\ShopwareInstall\Services\Database')
            ->addArgument(new Reference('utilities'));

        $this->container->register('shopware-install.demodata', 'Plugin\ShopwareInstall\Services\Demodata')
            ->addArgument(new Reference('utilities'))
            ->addArgument($this->container->get('path_provider'));

        $this->container->register('shopware_vcs_install_service', 'Plugin\ShopwareInstall\Services\Install\Vcs')
            ->addArgument(new Reference('shopware_checkout_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'));

        $this->container->register('shopware_release_install_service', 'Plugin\ShopwareInstall\Services\Install\Release')
            ->addArgument(new Reference('shopware_release_download_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'));
    }

}
