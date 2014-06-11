<?php
namespace Plugin\ShopwareRunCli\Command;

use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
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
            ->setHelp(<<<EOF
The <info>%command.name%</info> command allows you to trigger shopware cli commands from any subdirectory.
EOF
            );
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $shopwarePath = $input->getOption('shopwarePath');
        $arguments = implode(' ', $input->getArgument('sw-command'));

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        $shopwarePath = $this->getValidShopwarePath($shopwarePath, $output, $dialog);

        system("{$shopwarePath}/bin/console {$arguments}");

    }

    function getValidShopwarePath($shopwarePath=null, $output, DialogHelper $dialog)
    {
        if (!$shopwarePath) {
            $shopwarePath = realpath(getcwd());
        }

        do {
            if ($this->container->get('utilities')->isShopwareInstallation($shopwarePath)) {
                return $shopwarePath;
            }
        } while(($shopwarePath = dirname($shopwarePath)) && $shopwarePath != '/');

        return $dialog->askAndValidate($output, "Path to your Shopware installation: ", array($this->container->get('utilities'), 'validateShopwarePath'));

    }


}