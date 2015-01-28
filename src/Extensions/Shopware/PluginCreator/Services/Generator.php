<?php

namespace Shopware\PluginCreator\Services;

use Shopware\PluginCreator\Services\IoAdapter\IoAdapter;
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
     * @var IoAdapter\IoAdapter
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
     * Creates a given directory
     *
     * @param $path
     * @param  bool              $throwExceptionIfExists
     * @throws \RuntimeException
     */
    private function createDirectory($path, $throwExceptionIfExists = false)
    {
        if (file_exists($path)) {
            if (!$throwExceptionIfExists) {
                return;
            }
            throw new \RuntimeException("Could not create »{$path}«. Directory already exists");
        }

        $success = mkdir($path, 0777, true);

        if (!$success) {
            throw new \RuntimeException("Could not create »{$path}«. Check your directory permission");
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
     * Will step through all known template files, render and copy them to the configured destination
     */
    private function processTemplateFiles()
    {
        /**
         * These main files will always be created
         */
        $this->createFilesFromTemplate(array(
            "Bootstrap.tpl" => "Bootstrap.php",
            "Readme.tpl" => "Readme.md",
            "LICENSE" => "LICENSE",
            "plugin.tpl" => "plugin.json",
            "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
            "phpunit.xml.dist" => "phpunit.xml.dist",
            "tests/Test.tpl" => "tests/Test.php"
        ));

        /**
         * ControllerPath subscriber needed for frontend, backend, api and widget controllers
         */
        if ($this->configuration->hasBackend || $this->configuration->hasFrontend || $this->configuration->hasWidget || $this->configuration->hasApi) {
            $this->createFilesFromTemplate(array(
                "Subscriber/ControllerPath.tpl" => "Subscriber/ControllerPath.php",
            ));
        }

        /**
         * Creates the backend application and the controller
         */
        if ($this->configuration->hasBackend) {
            $this->createFilesFromTemplate(array(
                "Views/backend/application/app.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/app.js",
                "Views/backend/application/controller/main.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/controller/main.js",
                "Views/backend/application/model/main.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/model/main.js",
                "Views/backend/application/store/main.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/store/main.js",
                "Views/backend/application/view/detail/container.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/view/detail/container.js",
                "Views/backend/application/view/detail/window.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/view/detail/window.js",
                "Views/backend/application/view/list/list.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/view/list/list.js",
                "Views/backend/application/view/list/window.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/view/list/window.js",

            ));
        }

        /**
         * Creates controller, if widget or backend is needed
         */
        if ($this->configuration->hasBackend || $this->configuration->hasWidget) {
            $this->createFilesFromTemplate(array(
                "Controllers/Backend.tpl" => "Controllers/Backend/{$this->configuration->name}.php"
            ));
        }

        if ($this->configuration->hasWidget) {
            $this->createFilesFromTemplate(array(
                "Views/backend/widget/main.tpl" => "Views/backend/{$this->nameGenerator->under_score_js}/widgets/{$this->nameGenerator->under_score_js}.js",
            ));
            $this->createFilesFromTemplate(array(
                "Snippets/backend/widget/labels.tpl" => "Snippets/backend/widget/labels.ini"
            ));
        }

        if ($this->configuration->hasFrontend) {
            $this->createFilesFromTemplate(array(
                "Controllers/Frontend.tpl" => "Controllers/Frontend/{$this->configuration->name}.php",
                "Views/frontend/plugin_name/index.tpl" => "Views/frontend/{$this->nameGenerator->under_score_js}/index.tpl"
            ));

        }

        if ($this->configuration->hasApi) {
            $this->createFilesFromTemplate(array(
                "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/{$this->nameGenerator->camelCaseModel}.php",
                "Controllers/Api.tpl" => "Controllers/Api/{$this->nameGenerator->camelCaseModel}.php",

            ));

        }

        /**
         * Creates the model
         */
        if ($this->configuration->hasModels) {
            $this->createFilesFromTemplate(array(
                "Models/Model.tpl" => "Models/{$this->configuration->name}/{$this->nameGenerator->camelCaseModel}.php",
                "Models/Repository.tpl" => "Models/{$this->configuration->name}/Repository.php"
            ));
        }

        /**
         * Creates the command
         */
        if ($this->configuration->hasCommands) {
            $this->createFilesFromTemplate(array(
                "Commands/Command.tpl" => "Commands/{$this->nameGenerator->camelCaseModel}.php"
            ));
        }
    }

}
