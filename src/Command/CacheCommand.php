<?php

namespace ShopwareCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear the *internal* cache of the CLI tools (used for e.g. plugin repos)
 *
 * Class CacheCommand
 * @package ShopwareCli\Command
 */
class CacheCommand extends BaseCommand
{
    protected $utilities;
    protected $zipDir;

    protected function configure()
    {
        $this->setName('cli:cache:clear')
            ->setDescription('Clear the repository cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('cache')->clear();
    }

}
