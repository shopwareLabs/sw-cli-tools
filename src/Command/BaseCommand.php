<?php

namespace ShopwareCli\Command;

use ShopwareCli\OutputWriter\WrappedOutputWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The BaseCommand takes care of the container and also sets the outputwriter
 *
 * Class BaseCommand
 * @package ShopwareCli\Command
 */
abstract class BaseCommand extends Command implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Will register the 'writeln' method of the output interface as WrappedOutputWriter to the DI.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int|void
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->container->set('output_writer', new WrappedOutputWriter(array($output, 'writeln')));

        parent::run($input, $output);
    }
}
