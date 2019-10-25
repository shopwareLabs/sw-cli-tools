<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\RunCli;

use Shopware\RunCli\Command\RunCliCommand;
use ShopwareCli\Application\ConsoleAwareExtension;

class Bootstrap implements ConsoleAwareExtension
{
    /**
     * {@inheritdoc}
     */
    public function getConsoleCommands()
    {
        return [
            new RunCliCommand(),
        ];
    }
}
