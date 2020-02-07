<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear the *internal* cache of the CLI tools (used for e.g. plugin repos)
 */
class CacheCommand extends BaseCommand
{
    protected $utilities;

    protected $zipDir;

    protected function configure(): void
    {
        $this->setName('cli:cache:clear')
            ->setDescription('Clear the repository cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->container->get('cache')->clear();

        return 0;
    }
}
