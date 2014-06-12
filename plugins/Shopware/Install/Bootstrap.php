<?php

namespace Shopware\Install;

use Shopware\Install\Command\ShopwareInstallVcsCommand;
use Shopware\Install\Command\ShopwareInstallReleaseCommand;
use Shopware\Install\Command\ShopwareClearCacheCommand;
use ShopwareCli\Application\ConsoleAwarePlugin;
use ShopwareCli\Application\ContainerAwarePlugin;
use ShopwareCli\Application\DependencyInjection;
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
        $this->container->register('shopware_checkout_service', 'Shopware\Install\Services\Checkout')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('logger'));

        $this->container->register('shopware_release_download_service', 'Shopware\Install\Services\ReleaseDownloader')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('logger'))
            ->addArgument($this->container->get('path_provider')->getCachePath());

        $this->container->register('shopware-install.vcs_generator', 'Shopware\Install\Services\VcsGenerator');
        $this->container->register('shopware-install.config_writer', 'Shopware\Install\Services\ConfigWriter');
        $this->container->register('shopware-install.database', 'Shopware\Install\Services\Database')
            ->addArgument(new Reference('utilities'));

        $this->container->register('shopware-install.demodata', 'Shopware\Install\Services\Demodata')
            ->addArgument(new Reference('utilities'))
            ->addArgument($this->container->get('path_provider'));

        $this->container->register('shopware_vcs_install_service', 'Shopware\Install\Services\Install\Vcs')
            ->addArgument(new Reference('shopware_checkout_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'));

        $this->container->register('shopware_release_install_service', 'Shopware\Install\Services\Install\Release')
            ->addArgument(new Reference('shopware_release_download_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'));
    }

}
