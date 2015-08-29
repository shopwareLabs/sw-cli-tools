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
use Shopware\PluginCreator\Services\TemplateFileProvider\FilterFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\FrontendFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\ModelFileProvider;
use Shopware\PluginCreator\Services\TemplateFileProvider\WidgetFileProvider;
use Shopware\PluginCreator\Struct\Configuration;

class Generator
{
    const TEMPLATE_DIRECTORY = 'template';

    protected $outputDirectory = null;
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
     * @param IoAdapter     $ioAdapter
     * @param Configuration $configuration
     * @param NameGenerator $nameGenerator
     * @param Template      $template
     */
    public function __construct(IoAdapter $ioAdapter, Configuration $configuration, NameGenerator $nameGenerator, Template $template)
    {
        $this->configuration = $configuration;
        $this->template = $template;
        $this->nameGenerator = $nameGenerator;
        $this->ioAdapter = $ioAdapter;
    }

    /**
     * Returns path where the plugin should be created
     *
     * @return string
     */
    public function getOutputDirectory()
    {
        if (is_null($this->outputDirectory)) {
            $basePath = getcwd();
            $this->outputDirectory = $basePath . '/' . $this->configuration->name . '/';
        }

        return $this->outputDirectory;
    }

    public function setOutputDirectory($path)
    {
        $this->outputDirectory = $path;
    }

    /**
     * Creates the actual plugin from template files
     */
    public function run()
    {
        $this->configureTemplate();

        $path = $this->getOutputDirectory();
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

            $this->ioAdapter->createDirectory(dirname($this->getOutputDirectory() . $to));

            $this->ioAdapter->createFile($this->getOutputDirectory() . $to, $fileContent);
        }
    }

    /**
     * setup the template
     */
    private function configureTemplate()
    {
        $this->template->assign('configuration', $this->configuration);
        $this->template->assign('names', $this->nameGenerator);
        $this->template->setTemplatePath(dirname(__DIR__) . '/' . self::TEMPLATE_DIRECTORY);
    }

    /**
     * @return FileProviderInterface[]
     */
    private function getTemplateFileProvider()
    {
        return [
            new ApiFileProvider(),
            new BackendControllerFileProvider(),
            new BackendFileProvider(),
            new CommandFileProvider(),
            new ControllerPathFileProvider(),
            new DefaultFileProvider(),
            new FilterFileProvider(),
            new FrontendFileProvider(),
            new ModelFileProvider(),
            new WidgetFileProvider(),
            new FrontendFileProvider()
        ];
    }

    /**
     * Will step through all known template files, render and copy them to the configured destination
     */
    private function processTemplateFiles()
    {
        foreach ($this->getTemplateFileProvider() as $provider) {
            $this->createFilesFromTemplate(
                $provider->getFiles($this->configuration, $this->nameGenerator)
            );
        }
    }

}
