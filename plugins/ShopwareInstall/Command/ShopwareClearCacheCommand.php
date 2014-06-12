<?php
namespace Plugin\ShopwareInstall\Command;

use ShopwareCli\Application\Logger;
use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Utilities;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopwareClearCacheCommand extends BaseCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clear the shopware cache.')
            ->addOption(
                'installDir',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Install directory'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> clears the shopware cache
EOF
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->checkPath($input, $output);

        if (strpos($path, '/') !== 0) {
            $path = './' . $path;
        }

        Logger::setLogLevel(Logger::LEVEL_INFO);
        Logger::setOutputWriter($this->container->get('output_writer'));

        /** @var Utilities $utilities */
        $utilities = $this->container->get('utilities');
        echo $utilities->executeCommand("{$path}/cache/clear_cache.sh");

    }

    public function validateShopwareDirectory($path)
    {
        if (!$this->container->get('utilities')->isShopwareInstallation($path)) {
            throw new \RuntimeException("'$path' is not a valid shopware path");
        }

        return rtrim($path, '/');
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return mixed
     */
    private function checkPath(InputInterface $input, OutputInterface $output)
    {
        $shopwarePath  = $input->getOption('installDir');

        /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        $shopwarePath = $this->container->get('utilities')->getValidShopwarePath($shopwarePath, $output, $dialog);

        $input->setOption('installDir', $shopwarePath );

        return $shopwarePath ;
    }

}
