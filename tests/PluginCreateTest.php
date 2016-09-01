<?php

namespace ShopwareCli\tests;

use Shopware\PluginCreator\Services\Generator;
use Shopware\PluginCreator\Services\IoAdapter\Dummy;
use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\Template;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class PluginCreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Each fileProvider basically provides three infos:
     *  * name (array key + "$FileProvider")
     *  * config flag that will trigger this file provider
     *  * array of files (key = source template, value = target file)
     * @var array
     */
    protected $fileProvider = [
        'Api' => [
            'config' => 'hasApi',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/Test.php",
                FileProviderInterface::LEGACY_DIR . "Controllers/Api.tpl" => "Controllers/Api/Test.php"
            ]
        ],
        'BackendController' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Controllers/Backend.tpl" => "Controllers/Backend/SwagTest.php"
            ]
        ],
        'Backend' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/app.tpl" => "Resources/views/backend/swag_test/app.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/controller/main.tpl" => "Resources/views/backend/swag_test/controller/main.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/model/main.tpl" => "Resources/views/backend/swag_test/model/main.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/store/main.tpl" => "Resources/views/backend/swag_test/store/main.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/view/detail/container.tpl" => "Resources/views/backend/swag_test/view/detail/container.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/view/detail/window.tpl" => "Resources/views/backend/swag_test/view/detail/window.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/view/list/list.tpl" => "Resources/views/backend/swag_test/view/list/list.js",
                FileProviderInterface::LEGACY_DIR . "Resources/views/backend/application/view/list/window.tpl" => "Resources/views/backend/swag_test/view/list/window.js",
            ]
        ],
        'Command' => [
            'config' => 'hasCommands',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Commands/Command.tpl" => "Commands/Test.php"
            ]
        ],
        'ControllerPath' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Subscriber/ControllerPath.tpl" => "Subscriber/ControllerPath.php",
            ]
        ],
        'Default' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "PluginClass.tpl" => "SwagTest.php",
                FileProviderInterface::CURRENT_DIR . "Readme.tpl" => "Readme.md",
                FileProviderInterface::CURRENT_DIR . "LICENSE" => "LICENSE",
                FileProviderInterface::CURRENT_DIR . "plugin.xml.tpl" => "plugin.xml",
                FileProviderInterface::CURRENT_DIR . "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
                FileProviderInterface::CURRENT_DIR . "phpunit.xml.dist.tpl" => "phpunit.xml.dist",
                FileProviderInterface::CURRENT_DIR . "tests/PluginTest.tpl" => "tests/PluginTest.php",
                FileProviderInterface::CURRENT_DIR . "Resources/services.xml.tpl" => "Resources/services.xml",
                FileProviderInterface::CURRENT_DIR . "Resources/config.xml.tpl" => "Resources/config.xml",
                FileProviderInterface::CURRENT_DIR . "Resources/menu.xml.tpl" => "Resources/menu.xml"
            ]
        ],
        'Filter' => [
            'config' => 'hasFilter',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/Condition/Condition.tpl" => "Components/SearchBundleDBAL/Condition/SwagTestCondition.php",
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/Condition/ConditionHandler.tpl" => "Components/SearchBundleDBAL/Condition/SwagTestConditionHandler.php",
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/Facet/Facet.tpl" => "Components/SearchBundleDBAL/Facet/SwagTestFacet.php",
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/Facet/FacetHandler.tpl" => "Components/SearchBundleDBAL/Facet/SwagTestFacetHandler.php",
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/CriteriaRequestHandler.tpl" => "Components/SearchBundleDBAL/SwagTestCriteriaRequestHandler.php",
                FileProviderInterface::CURRENT_DIR . "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php"
            ]
        ],
        'Frontend' => [
            'config' => 'hasFrontend',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Resources/Controllers/Frontend.tpl" => "Controllers/Frontend/SwagTest.php",
                FileProviderInterface::CURRENT_DIR . "Resources/views/frontend/plugin_name/index.tpl" => "Resources/views/frontend/swag_test/index.tpl"
            ]
        ],
        'Model' => [
            'config' => 'hasModels',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Models/Model.tpl" => "Models/Test.php",
                FileProviderInterface::CURRENT_DIR . "Models/Repository.tpl" => "Models/Repository.php"
            ]
        ],
        'Widget' => [
            'config' => 'hasWidget',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/widget/main.tpl" => "Resources/views/backend/swag_test/widgets/swag_test.js",
                FileProviderInterface::CURRENT_DIR . "Resources/snippets/backend/widget/labels.tpl" => "Resources/snippets/backend/widget/labels.ini"
            ]
        ],
    ];

    private function getConfigObject()
    {
        $config = new Configuration();

        $config->hasBackend = false;
        $config->hasApi = false;
        $config->hasWidget = false;
        $config->hasFrontend = false;
        $config->hasCommands = false;
        $config->hasModels = true;
        $config->name = 'SwagTest';

        return $config;
    }

    public function testLegacyFileProvider()
    {
        foreach ($this->fileProvider as $name => $provider) {
            $config = $this->getConfigObject();
            $configName = $provider['config'];
            $config->$configName = true;
            $config->backendModel = 'SwagTest\Models\Test';

            $ioAdapter = new Dummy();
            $generator = new Generator($ioAdapter, $config, new NameGenerator($config), new Template());
            $generator->setOutputDirectory('');

            $generator->run();

            // Test, if the file provider files, do exist
            foreach ($provider['files'] as $file) {
                $this->assertTrue(
                    in_array($file, array_keys($ioAdapter->getFiles())),
                    "{$file} not found in generated files"
                );
            }

            // merge all provider files into one array
            $allProviderFiles = array_reduce(
                array_column($this->fileProvider, 'files'),
                function ($a, $b) {
                    $a = $a ?: [];
                    $b = $b ?: [];
                    return array_merge($a, $b);
                });

            // Test, if existing files are defined by a file provider
            foreach (array_keys($ioAdapter->getFiles()) as $file) {
                $this->assertTrue(
                    in_array($file, $allProviderFiles),
                    "{$file} is not defined by any file provider"
                );
            }
        }
    }

    /**
     * Foreach file provider: Create a plugin which needs this file provider and check,
     * if all required / pre-defined files actually exists.
     */
    public function testFileProvider()
    {
        foreach ($this->fileProvider as $name => $provider) {
            $config = $this->getConfigObject();
            $configName = $provider['config'];
            $config->$configName = true;
            $config->backendModel = 'SwagTest\Models\Test';

            $ioAdapter = new Dummy();
            $generator = new Generator($ioAdapter, $config, new NameGenerator($config), new Template());
            $generator->setOutputDirectory('');

            $generator->run();

            // Test, if the file provider files, do exist
            foreach ($provider['files'] as $file) {
                $this->assertTrue(
                    in_array($file, array_keys($ioAdapter->getFiles())),
                    "{$file} not found in generated files"
                );
            }

            // merge all provider files into one array
            $allProviderFiles = array_reduce(
                array_column($this->fileProvider, 'files'),
                function ($a, $b) {
                    $a = $a ?: [];
                    $b = $b ?: [];
                    return array_merge($a, $b);
                });

            // Test, if existing files are defined by a file provider
            foreach (array_keys($ioAdapter->getFiles()) as $file) {
                $this->assertTrue(
                    in_array($file, $allProviderFiles),
                    "{$file} is not defined by any file provider"
                );
            }
        }
    }
}
