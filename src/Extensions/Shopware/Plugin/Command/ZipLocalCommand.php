<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Command;

use Shopware\Plugin\Struct\PluginBootstrap;
use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Zip a plugin
 */
class ZipLocalCommand extends BaseCommand
{
    /**
     * @var string
     */
    protected $zipDir;

    public function validatePluginDir(string $dir): void
    {
        $fileName = \basename($dir);

        if (!\file_exists($dir . '/Bootstrap.php') && !\file_exists($dir . '/' . $fileName . '.php')) {
            throw new \RuntimeException("Could not find Bootstrap.php or $fileName.php in $dir");
        }
    }

    /**
     * @param string $pluginDirectory
     */
    public function doZip($pluginDirectory): void
    {
        if (\file_exists($pluginDirectory . '/Bootstrap.php')) {
            /** @var PluginBootstrap $info */
            $info = $this->container->get('bootstrap_info')->analyze($pluginDirectory . '/Bootstrap.php');

            $outputFile = $this->getZipDir() . '/' . $info->name . '.zip';
            $tempDir = $this->getTempDir();
            $sourceDir = $tempDir . '/' . $info->module . '/' . $info->name;
            if (!\is_dir($sourceDir) && !\mkdir($sourceDir, 0777, true) && !\is_dir($sourceDir)) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $sourceDir));
            }

            $this->container->get('process_executor')->execute("cp -r {$pluginDirectory}/* $sourceDir");
            $this->container->get('utilities')->changeDir($tempDir);

            $this->container->get('zip_service')->zipDir($info->module . '/' . $info->name, $outputFile);
        } else {
            $pluginName = \basename($pluginDirectory);
            $outputFile = $this->getZipDir() . '/' . $pluginName . '.zip';

            $tempDir = $this->getTempDir();
            $sourceDir = $tempDir . '/' . $pluginName;
            if (!\is_dir($sourceDir) && !\mkdir($sourceDir, 0777, true) && !\is_dir($sourceDir)) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $sourceDir));
            }

            $this->container->get('process_executor')->execute("cp -r {$pluginDirectory}/* $sourceDir");
            $this->container->get('utilities')->changeDir($tempDir);

            $this->container->get('zip_service')->zipDir($pluginName, $outputFile);
        }

        $this->container->get('io_service')->writeln("<info>Created file $outputFile</info>");
    }

    protected function configure(): void
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

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->zipDir = (string) \getcwd();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $input->getArgument('dir');
        $directory = \rtrim($directory, '/');
        $this->validatePluginDir($directory);

        $this->doZip($directory);

        return Command::SUCCESS;
    }

    protected function getTempDir(): string
    {
        $tempDirectory = \sys_get_temp_dir();
        $tempDirectory .= '/plugin-inst-' . \uniqid('', true);
        if (!\is_dir($tempDirectory) && !\mkdir($tempDirectory, 0777, true) && !\is_dir($tempDirectory)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $tempDirectory));
        }

        return $tempDirectory;
    }

    protected function getZipDir(): string
    {
        return $this->zipDir;
    }
}
