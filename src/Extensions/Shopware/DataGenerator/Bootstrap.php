<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator;

use Shopware\DataGenerator\Struct\Config;
use Shopware\DataGenerator\Writer\WriterManager;
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

        $this->container->register('random_data_provider', RandomDataProvider::class);

        $this->container->register('generator_config', Config::class);

        $this->container->register('resource_loader', ResourceLoader::class)
            ->addArgument(new Reference('service_container'));

        $this->container->register('data_generator', DataGenerator::class)
            ->addArgument(new Reference('random_data_provider'))
            ->addArgument(new Reference('resource_loader'))
            ->addArgument(new Reference('generator_config'));

        $this->container->register('writer_manager', WriterManager::class)
            ->addArgument(new Reference('generator_config'))
            ->addArgument(new Reference('io_service'));
    }

    /**
     * Return an array with instances of your console commands here
     */
    public function getConsoleCommands()
    {
        return [
            new Command\CreateDataCommand(),
        ];
    }
}
