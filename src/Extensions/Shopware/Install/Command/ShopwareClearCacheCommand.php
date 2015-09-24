<?php
namespace Shopware\Install\Command;

use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\ProcessExecutor;
use ShopwareCli\Services\ShopwareInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setHelp('The <info>%command.name%</info> clears the shopware cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->checkPath($input);

        if (strpos($path, '/') !== 0) {
            $path = './'.$path;
        }

        /** @var $shopwareInfo ShopwareInfo */
        $shopwareInfo = $this->container->get('shopware_info');

        /** @var ProcessExecutor $processExecutor */
        $processExecutor = $this->container->get('process_executor');
        $processExecutor->execute($shopwareInfo->getCacheDir($path)."/clear_cache.sh");
    }

    /**
     * @param  string $path
     * @return string
     * @throws \RuntimeException
     */
    public function validateShopwareDirectory($path)
    {
        if (!$this->container->get('utilities')->isShopwareInstallation($path)) {
            throw new \RuntimeException("'$path' is not a valid shopware path");
        }

        return rtrim($path, '/');
    }

    /**
     * @param  InputInterface $input
     * @return string
     */
    private function checkPath(InputInterface $input)
    {
        $shopwarePath = $input->getOption('installDir');

        $shopwarePath = $this->container->get('utilities')->getValidShopwarePath($shopwarePath);

        $input->setOption('installDir', $shopwarePath);

        return $shopwarePath;
    }
}
