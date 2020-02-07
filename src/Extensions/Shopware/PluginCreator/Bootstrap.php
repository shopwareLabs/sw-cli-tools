<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator;

use Shopware\PluginCreator\Command\CreatePluginCommand;
use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap implements ContainerAwareExtension, ConsoleAwareExtension
{
    protected $container;

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Return an array with instances of your console commands here
     */
    public function getConsoleCommands()
    {
        return [
            new CreatePluginCommand(),
        ];
    }
}
