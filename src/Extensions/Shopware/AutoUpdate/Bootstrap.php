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

        $config = $this->container->get('config');
        $manifest = $config['general']['manifestUrl'];

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

    public function isPharFile()
    {
        $config = $this->container->get('config');
        if (!isset($config['general']['manifestUrl'])) {
            return false;
        }

        $toolPath = $this->container->get('path_provider')->getCliToolPath();
        return strpos($toolPath, 'phar:') !== false ;
    }

}
