<?php

namespace Shopware\PluginCreator\Services;

use Shopware\PluginCreator\Services\IoAdapter\IoAdapter;
use Shopware\PluginCreator\Services\TemplateFileProvider\ApiFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\BackendControllerFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\BackendFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\CommandFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\ControllerPathFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\DefaultFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderLoaderInterface;
use Shopware\PluginCreator\Services\TemplateFileProvider\FilterFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\FrontendFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\LegacyOptionFileProviderLoader;
use Shopware\PluginCreator\Services\TemplateFileProvider\ModelFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\WidgetFileProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\OutputDirectoryProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

class Generator
{
    const TEMPLATE_DIRECTORY = 'template';

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

    /**
     * @param IoAdapter $ioAdapter
     * @param Configuration $configuration
     * @param NameGenerator $nameGenerator
     * @param Template $template
     * @param FileProviderLoaderInterface $fileProviderLoader
     * @param OutputDirectoryProviderInterface $outputDirectoryProvider
     */
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
    public function run()
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
     *
     * @param $files
     */
    private function createFilesFromTemplate($files)
    {
        foreach ($files as $from => $to) {
            $fileContent = $this->template->fetch($from);

            $path = $this->outputDirectoryProvider->getPath();
            $this->ioAdapter->createDirectory(dirname($path.$to));

            $this->ioAdapter->createFile($path.$to, $fileContent);
        }
    }

    /**
     * setup the template
     */
    private function configureTemplate()
    {
        $this->template->assign('configuration', $this->configuration);
        $this->template->assign('names', $this->nameGenerator);
        $this->template->setTemplatePath(dirname(__DIR__).'/'.self::TEMPLATE_DIRECTORY);
    }

    /**
     * Will step through all known template files, render and copy them to the configured destination
     */
    private function processTemplateFiles()
    {
        foreach ($this->fileProviderLoader->load() as $provider) {
            $this->createFilesFromTemplate(
                $provider->getFiles($this->configuration, $this->nameGenerator)
            );
        }
    }
}
