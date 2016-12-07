<?php

namespace Shopware\DataGenerator;

use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Bootstrap implements ContainerAwareExtension, ConsoleAwareExtension
{
    protected $container;

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;

        $this->container->register('random_data_provider', '\Shopware\DataGenerator\RandomDataProvider');

        $this->container->register('generator_config', '\Shopware\DataGenerator\Struct\Config');

        $this->container->register('resource_loader', '\Shopware\DataGenerator\ResourceLoader')
            ->addArgument(new Reference('service_container'));

        $this->container->register('data_generator', '\Shopware\DataGenerator\DataGenerator')
            ->addArgument(new Reference('random_data_provider'))
            ->addArgument(new Reference('resource_loader'))
            ->addArgument(new Reference('generator_config'))
            ->addArgument(new Reference('io_service'));

        $this->container->register('writer_manager', '\Shopware\DataGenerator\Writer\WriterManager')
            ->addArgument(new Reference('generator_config'))
            ->addArgument(new Reference('io_service'));
    }

    /**
     * Return an array with instances of your console commands here
     *
     * @return mixed
     */
    public function getConsoleCommands()
    {
        return [
            new Command\CreateDataCommand(),
        ];
    }
}
