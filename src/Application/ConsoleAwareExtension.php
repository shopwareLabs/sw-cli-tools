<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Application;

use Symfony\Component\Console\Command\Command;

interface ConsoleAwareExtension
{
    /**
     * Return an array with instances of your console commands here
     *
     * @return Command[]
     */
    public function getConsoleCommands();
}
