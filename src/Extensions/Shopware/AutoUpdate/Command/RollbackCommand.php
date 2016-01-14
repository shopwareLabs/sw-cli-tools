<?php
namespace Shopware\AutoUpdate\Command;

use Humbug\SelfUpdate\Updater;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Services\ShopwareInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends BaseCommand
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
        $this->setName('rollback');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $result = $this->updater->rollback();
            if (!$result) {
                $output->writeln("Rollback failed!");
                return 1;
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            $output->writeln("Unable to rollback");
            return 1;
        }

        $output->writeln("Rollback successful");
        return 0;
    }
}
