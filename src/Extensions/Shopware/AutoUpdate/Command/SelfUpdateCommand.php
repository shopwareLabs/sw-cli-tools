<?php
namespace Shopware\AutoUpdate\Command;

use Humbug\SelfUpdate\Updater;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Services\ShopwareInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    protected function configure()
    {
        $this->setName('self-update');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = $this->updater->update();
            if (!$result) {
                $output->writeln('No update needed.');
                return 0;
            }

            $new = $this->updater->getNewVersion();
            $old = $this->updater->getOldVersion();

            $output->writeln(sprintf(
                'Updated from SHA-1 %s to SHA-1 %s. Please run again', $old, $new
            ));

            return 0;
        } catch (\Exception $e) {
            $output->writeln('Unable to update. Please check your connection');
            $this->printException($output, $e);

            return 1;
        }
    }

    /**
     * @param OutputInterface $output
     * @param \Exception $exception
     */
    protected function printException(OutputInterface $output, \Exception $exception)
    {
        do {
            $output->writeln(sprintf('(%d) %s in %s:%d:', $exception->getCode(), $exception->getMessage(),            $exception->getFile(), $exception->getLine()));
            $trace = $exception->getTraceAsString();
            $trace = preg_replace('/^/m', '    ', $trace);
            $trace = preg_replace("/(\r?\n)/s", PHP_EOL, $trace);
            $output->writeln($trace);
        } while ($exception = $exception->getPrevious());
    }
}
