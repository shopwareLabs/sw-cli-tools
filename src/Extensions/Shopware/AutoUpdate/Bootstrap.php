<?php

namespace Shopware\AutoUpdate;

use Humbug\SelfUpdate\Updater;
use Shopware\AutoUpdate\Command\RollbackCommand;
use Shopware\AutoUpdate\Command\SelfUpdateCommand;
use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Provides self update capability
 *
 * Class Bootstrap
 * @package Shopware\AutoUpdate
 */
class Bootstrap implements ConsoleAwareExtension, ContainerAwareExtension
{
    /** @var  ContainerBuilder */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;

        if (!$this->isPharFile()) {
            return;
        }

        $this->populateContainer($container);
    }


    /**
     * {@inheritdoc}
     */
    public function getConsoleCommands()
    {
        if (!$this->isPharFile()) {
            return array();
        }

        if ($this->checkUpdateOnRun()) {
            $this->runUpdate();
        }

        return array(
            new SelfUpdateCommand($this->container->get('updater')),
            new RollbackCommand($this->container->get('updater')),
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function populateContainer($container)
    {
        $container->set('updater', $this->createUpdater());
    }

    /**
     * @return Updater
     */
    private function createUpdater()
    {
        $config     = $this->container->get('config');
        $pharUrl    = $config['update']['pharUrl'];
        $versionUrl = $config['update']['vesionUrl'];
        $verifyKey  = (bool)$config['update']['verifyPublicKey'];

        $updater = new Updater(null, $verifyKey);
        $updater->getStrategy()->setPharUrl($pharUrl);
        $updater->getStrategy()->setVersionUrl($versionUrl);

        return $updater;
    }


    /**
     * Checks if script is run as phar archive and manifestUrl is available
     *
     * @return bool
     */
    public function isPharFile()
    {
        $toolPath = $this->container->get('path_provider')->getCliToolPath();

        return strpos($toolPath, 'phar:') !== false ;
    }

    /**
     * perform update on the fly
     */
    private function runUpdate()
    {
        $updater = $this->container->get('updater');

        try {
            $result = $updater->update();
            if (!$result) {
                return;
            }

            $new = $updater->getNewVersion();
            $old = $updater->getOldVersion();

            exit(sprintf(
                "Updated from SHA-1 %s to SHA-1 %s. Please run again\n", $old, $new
            ));
        } catch (\Exception $e) {
            echo "\nCheck your connection\n";
            exit(1);
        }
    }

    /**
     * @return bool
     */
    private function checkUpdateOnRun()
    {
        $config = $this->container->get('config');
        if (!isset($config['update']['checkOnStartup'])) {
            return false;
        }

        return $config['update']['checkOnStartup'];
    }
}
