<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read the internal CLI cache. Used for e.g. plugin repos
 */
class CacheGetCommand extends BaseCommand
{
    protected $utilities;

    protected $zipDir;

    protected function configure(): void
    {
        $this->setName('cli:cache:get')
            ->setDescription('Read the cache')
            ->addArgument(
                'keys',
                InputArgument::IS_ARRAY,
                'One or more cache keys to read'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keys = $input->getArgument('keys');

        if (empty($keys)) {
            foreach ($this->container->get('cache')->getKeys() as $key) {
                $output->writeln($key);
            }

            return 0;
        }

        foreach ($keys as $key) {
            $output->writeln("<question>{$key}</question>");
            $output->writeln($this->container->get('cache')->read($key));
        }

        return 0;
    }
}
