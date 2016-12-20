<?php
namespace Shopware\RunCli\Command;

use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCliCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run shopware console commands from shopware subdirectories.')
            ->addOption(
                'shopwarePath',
                null,
                InputOption::VALUE_OPTIONAL,
                'Your shopware path.',
                ''
            )
            ->addArgument(
                'sw-command',
                InputArgument::IS_ARRAY,
                'arguments for your shopare command'
            )
            ->setHelp(
<<<EOF
The <info>%command.name%</info> command allows you to trigger shopware cli commands from any subdirectory.
EOF
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $shopwarePath = $input->getOption('shopwarePath');
        $arguments = implode(' ', $input->getArgument('sw-command'));

        /** @var $ioService IoService */
        $ioService = $this->container->get('io_service');
        $shopwarePath = $this->getValidShopwarePath($shopwarePath, $ioService);

        system("{$shopwarePath}/bin/console {$arguments}");
    }

    /**
     * @param  string    $shopwarePath
     * @param  IoService $ioService
     * @return string
     */
    public function getValidShopwarePath($shopwarePath, IoService $ioService)
    {
        if (!$shopwarePath) {
            $shopwarePath = realpath(getcwd());
        }

        do {
            if ($this->container->get('utilities')->isShopwareInstallation($shopwarePath)) {
                return $shopwarePath;
            }
        } while (($shopwarePath = dirname($shopwarePath)) && $shopwarePath != '/');

        return $ioService->askAndValidate(
            'Path to your Shopware installation: ',
            [$this->container->get('utilities'), 'validateShopwarePath']
        );
    }
}
