<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\AutoUpdate\Command;

use Humbug\SelfUpdate\Updater;
use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends BaseCommand
{
    /**
     * @var Updater
     */
    private $updater;

    public function __construct(Updater $updater)
    {
        $this->updater = $updater;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('self-update');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->updater->update();
            if (!$result) {
                $output->writeln('No update needed.');

                return Command::SUCCESS;
            }

            $new = $this->updater->getNewVersion();
            $old = $this->updater->getOldVersion();

            $output->writeln(\sprintf(
                'Updated from SHA-1 %s to SHA-1 %s. Please run again',
                $old,
                $new
            ));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Unable to update. Please check your connection');
            $this->printException($output, $e);

            return Command::FAILURE;
        }
    }

    protected function printException(OutputInterface $output, \Exception $exception): void
    {
        do {
            $output->writeln(\sprintf('(%d) %s in %s:%d:', $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine()));
            $trace = $exception->getTraceAsString();
            $trace = \preg_replace('/^/m', '    ', $trace);
            $trace = \preg_replace("/(\r?\n)/s", \PHP_EOL, $trace);
            $output->writeln($trace);
        } while ($exception = $exception->getPrevious());
    }
}
