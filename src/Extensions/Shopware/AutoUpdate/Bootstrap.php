<?php

namespace Shopware\AutoUpdate;

use ShopwareCli\Application\ConsoleAwareExtension;

use KevinGH\Amend\Command;
use KevinGH\Amend\Helper;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    public function getConsoleCommands()
    {
        if (!$this->isPharFile()) {
            return array();
        }

        if ($this->checkUpdateOnRun()) {
            $this->runUpdate();
        }
        
        $manifest = $this->getManifestUrl();

        $command = new Command('update');
        $command->setManifestUri($manifest);

        return array(
            $command
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    public function setContainer(ContainerBuilder $container = null)
    {
        $this->container = $container;

        if ($this->isPharFile()) {
            $container->get('helper_set')->set(new Helper());
        }
    }

    /**
     * Checks if script is run as phar archive and manifestUrl is available
     *
     * @return bool
     */
    public function isPharFile()
    {
        $config = $this->container->get('config');
        if (!isset($config['update']['manifestUrl'])) {
            return false;
        }

        $toolPath = $this->container->get('path_provider')->getCliToolPath();
        return strpos($toolPath, 'phar:') !== false ;
    }

    /**
     * perform update on the fly
     */
    private function runUpdate()
    {
        /** @var $amend Helper */
        $amend = $this->container->get('helper_set')->get('amend');

        try {
            $manager = $amend->getManager($this->getManifestUrl());
        } catch(\Herrera\Json\Exception\FileException $e) {
            echo "\nCould not download {$this->getManifestUrl()}\n";
            echo "\nCheck your connection\n";
            return;
        }

        if ($manager->update(
            \ShopwareCli\Application::VERSION,
            false,
            false
        )){
            echo "\nUpdated your archive. Please run again\n";
            exit(0);
        }
    }

    /**
     * Get manifest url
     *
     * @return mixed
     */
    private function getManifestUrl()
    {
        $config = $this->container->get('config');
        $manifest = $config['update']['manifestUrl'];
        return $manifest;
    }

    private function checkUpdateOnRun()
    {
        $config = $this->container->get('config');
        if (!isset($config['update']['checkOnStartup'])) {
            return false;
        }
        return $config['update']['checkOnStartup'];
    }

}
