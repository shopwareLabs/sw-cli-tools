<?php

namespace Shopware\Plugin\Command;

use Shopware\Plugin\Struct\PluginBootstrap;
use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Zip a plugin
 *
 * Class ZipCommand
 * @package ShopwareCli\Command
 */
class ZipLocalCommand extends BaseCommand
{
    protected $utilities;
    protected $zipDir;

    protected function configure()
    {
        $this
            ->setName('plugin:zip:dir')
            ->setDescription('Creates a installable plugin zip from a given plugin directory')
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Name of the plugin to install'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->zipDir = getcwd();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $directory = rtrim($directory, '/');
        $this->validatePluginDir($directory);

        $this->doZip($directory);
    }

    public function validatePluginDir($dir)
    {
        if (!file_exists($dir . '/Bootstrap.php')) {
            throw new \RuntimeException("Could not find Bootstrap.php in $dir");
        }
    }

    public function doZip($pluginDirectory)
    {
        /** @var PluginBootstrap $info */
        $info = $this->container->get('bootstrap_info')->analyze($pluginDirectory . '/Bootstrap.php');

        $outputFile = $this->getZipDir() . '/' . $info->name . '.zip';
        $tempDir = $this->getTempDir();
        $sourceDir = $tempDir . '/' . $info->module . '/' . $info->name;
        mkdir($sourceDir, 0777, true);

        exec("cp -r $pluginDirectory $sourceDir");
        $this->container->get('utilities')->changeDir($tempDir);

        $this->container->get('zip_service')->zipDir($info->module . '/' . $info->name, $outputFile);
        $this->container->get('io_service')->writeln("<info>Created file $outputFile</info>");
    }

    protected function getTempDir()
    {
        $tempDirectory = sys_get_temp_dir();
        $tempDirectory .= '/plugin-inst-' . uniqid();
        mkdir($tempDirectory, 0777, true);

        return $tempDirectory;
    }

    protected function getZipDir()
    {
        return $this->zipDir;
    }
}
