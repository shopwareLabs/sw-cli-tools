<?php

namespace ShopwareCli\Command;

use ShopwareCli\Plugin\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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