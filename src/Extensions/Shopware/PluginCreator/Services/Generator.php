<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services;

use Shopware\PluginCreator\Services\IoAdapter\IoAdapter;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderLoaderInterface;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\OutputDirectoryProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

class Generator
{
    private const TEMPLATE_DIRECTORY = 'template';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var NameGenerator
     */
    private $nameGenerator;

    /**
     * @var IoAdapter
     */
    private $ioAdapter;

    /**
     * @var FileProviderLoaderInterface
     */
    private $fileProviderLoader;

    /**
     * @var OutputDirectoryProviderInterface
     */
    private $outputDirectoryProvider;

    public function __construct(
        IoAdapter $ioAdapter,
        Configuration $configuration,
        NameGenerator $nameGenerator,
        Template $template,
        FileProviderLoaderInterface $fileProviderLoader,
        OutputDirectoryProviderInterface $outputDirectoryProvider
    ) {
        $this->configuration = $configuration;
        $this->template = $template;
        $this->nameGenerator = $nameGenerator;
        $this->ioAdapter = $ioAdapter;
        $this->fileProviderLoader = $fileProviderLoader;
        $this->outputDirectoryProvider = $outputDirectoryProvider;
    }

    /**
     * Creates the actual plugin from template files
     */
    public function run(): void
    {
        $this->configureTemplate();

        $path = $this->outputDirectoryProvider->getPath();
        if ($this->ioAdapter->exists($path)) {
            throw new \RuntimeException("Could not create »{$path}«. Directory already exists");
        }
        $this->ioAdapter->createDirectory($path);

        $this->processTemplateFiles();
    }

    /**
     * Creates files from an array of template files
     */
    private function createFilesFromTemplate($files): void
    {
        foreach ($files as $from => $to) {
            $fileContent = $this->template->fetch($from);

            $path = $this->outputDirectoryProvider->getPath();
            $this->ioAdapter->createDirectory(\dirname($path . $to));

            $this->ioAdapter->createFile($path . $to, $fileContent);
        }
    }

    /**
     * setup the template
     */
    private function configureTemplate(): void
    {
        $this->template->assign('configuration', $this->configuration);
        $this->template->assign('names', $this->nameGenerator);
        $this->template->setTemplatePath(\dirname(__DIR__) . '/' . self::TEMPLATE_DIRECTORY);
    }

    /**
     * Will step through all known template files, render and copy them to the configured destination
     */
    private function processTemplateFiles(): void
    {
        foreach ($this->fileProviderLoader->load() as $provider) {
            $this->createFilesFromTemplate(
                $provider->getFiles($this->configuration, $this->nameGenerator)
            );
        }
    }
}
