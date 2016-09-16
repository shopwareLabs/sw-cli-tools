<?php

namespace ShopwareCli\tests;

use Shopware\PluginCreator\Services\Generator;
use Shopware\PluginCreator\Services\IoAdapter\Dummy;
use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\Template;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Services\TemplateFileProvider\LegacyOptionFileProviderLoader;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\CurrentOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\LegacyOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\OutputDirectoryProviderInterface;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;
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
                FileProviderInterface::CURRENT_DIR . "Components/Api/Resource/Resource.tpl" => "Components/Api/Resource/Test.php",
                FileProviderInterface::CURRENT_DIR . "Controllers/Api.tpl" => "Controllers/Api/Test.php",
                FileProviderInterface::CURRENT_DIR . "Subscriber/ApiSubscriber.tpl" => "Subscriber/ApiSubscriber.php"
            ]
        ],
        'BackendController' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Controllers/Backend.tpl" => "Controllers/Backend/SwagTest.php"
            ]
        ],
        'Backend' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/app.tpl" => "Resources/views/backend/swag_test/app.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/controller/main.tpl" => "Resources/views/backend/swag_test/controller/main.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/model/main.tpl" => "Resources/views/backend/swag_test/model/main.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/store/main.tpl" => "Resources/views/backend/swag_test/store/main.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/view/detail/container.tpl" => "Resources/views/backend/swag_test/view/detail/container.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/view/detail/window.tpl" => "Resources/views/backend/swag_test/view/detail/window.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/view/list/list.tpl" => "Resources/views/backend/swag_test/view/list/list.js",
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/application/view/list/window.tpl" => "Resources/views/backend/swag_test/view/list/window.js",
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
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundle/CriteriaRequestHandler.tpl" => "Components/SearchBundle/SwagTestCriteriaRequestHandler.php",
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/Sorting/RandomSorting.tpl" => "Components/SearchBundleDBAL/Sorting/RandomSorting.php",
                FileProviderInterface::CURRENT_DIR . "Components/SearchBundleDBAL/Sorting/RandomSortingHandler.tpl" => "Components/SearchBundleDBAL/Sorting/RandomSortingHandler.php",
                FileProviderInterface::CURRENT_DIR . "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php",
                FileProviderInterface::CURRENT_DIR . "Resources/views/frontend/listing/actions/action-sorting.tpl" => "Resources/views/frontend/listing/actions/action-sorting.tpl"
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
                FileProviderInterface::CURRENT_DIR . "Resources/views/backend/widget/main.tpl" => "Resources/views/backend/widgets/swag_test.js",
                FileProviderInterface::CURRENT_DIR . "Resources/snippets/backend/widget/labels.tpl" => "Resources/snippets/backend/widget/labels.ini",
                FileProviderInterface::CURRENT_DIR . "Controllers/Backend/BackendWidget.tpl" => "Controllers/Backend/SwagTestWidget.php",
                FileProviderInterface::CURRENT_DIR . "Subscriber/BackendWidget.tpl" => "Subscriber/BackendWidget.php",
            ]
        ],
        'ElasticSearch' => [
            'config' => 'hasElasticSearch',
            'files' => [
                FileProviderInterface::CURRENT_DIR . 'Subscriber/ORMBacklogSubscriber.php' => 'Subscriber/ORMBacklogSubscriber.php',
                FileProviderInterface::CURRENT_DIR . 'Components/ESIndexingBundle/Struct/Blog.tpl' => 'Components/ESIndexingBundle/Struct/Blog.php',
                FileProviderInterface::CURRENT_DIR . 'Components/ESIndexingBundle/BlogDataIndexer.tpl' => 'Components/ESIndexingBundle/BlogDataIndexer.php',
                FileProviderInterface::CURRENT_DIR . 'Components/ESIndexingBundle/BlogMapping.tpl' => 'Components/ESIndexingBundle/BlogMapping.php',
                FileProviderInterface::CURRENT_DIR . 'Components/ESIndexingBundle/BlogProvider.tpl' => 'Components/ESIndexingBundle/BlogProvider.php',
                FileProviderInterface::CURRENT_DIR . 'Components/ESIndexingBundle/BlogSettings.tpl' => 'Components/ESIndexingBundle/BlogSettings.php',
                FileProviderInterface::CURRENT_DIR . 'Components/ESIndexingBundle/BlogSynchronizer.tpl' => 'Components/ESIndexingBundle/BlogSynchronizer.php',
                FileProviderInterface::CURRENT_DIR . 'Components/SearchBundleES/BlogSearch.tpl' => 'Components/SearchBundleES/BlogSearch.php'
            ]
        ],
    ];

    /**
     * @var array
     */
    protected $legacyFileProvider = [
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
                "Controllers/Backend.tpl" => "Controllers/Backend/SwagTest.php"
            ]
        ],
        'Backend' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/app.tpl" => "Views/backend/swag_test/app.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/controller/main.tpl" => "Views/backend/swag_test/controller/main.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/model/main.tpl" => "Views/backend/swag_test/model/main.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/store/main.tpl" => "Views/backend/swag_test/store/main.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/view/detail/container.tpl" => "Views/backend/swag_test/view/detail/container.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/view/detail/window.tpl" => "Views/backend/swag_test/view/detail/window.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/view/list/list.tpl" => "Views/backend/swag_test/view/list/list.js",
                FileProviderInterface::LEGACY_DIR . "Views/backend/application/view/list/window.tpl" => "Views/backend/swag_test/view/list/window.js",
            ]
        ],
        'Command' => [
            'config' => 'hasCommands',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Commands/Command.tpl" => "Commands/Test.php"
            ]
        ],
        'ControllerPath' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Subscriber/ControllerPath.tpl" => "Subscriber/ControllerPath.php",
            ]
        ],
        'Default' => [
            'config' => 'hasBackend',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Bootstrap.tpl" => "Bootstrap.php",
                FileProviderInterface::LEGACY_DIR . "Readme.tpl" => "Readme.md",
                FileProviderInterface::LEGACY_DIR . "LICENSE" => "LICENSE",
                FileProviderInterface::LEGACY_DIR . "plugin.tpl" => "plugin.json",
                FileProviderInterface::LEGACY_DIR . "Subscriber/Frontend.tpl" => "Subscriber/Frontend.php",
                FileProviderInterface::LEGACY_DIR . "phpunit.xml.dist.tpl" => "phpunit.xml.dist",
                FileProviderInterface::LEGACY_DIR . "tests/Test.tpl" => "tests/Test.php"
            ]
        ],
        'Filter' => [
            'config' => 'hasFilter',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Components/SearchBundleDBAL/Condition/Condition.tpl" => "Components/SearchBundleDBAL/Condition/SwagTestCondition.php",
                FileProviderInterface::LEGACY_DIR . "Components/SearchBundleDBAL/Condition/ConditionHandler.tpl" => "Components/SearchBundleDBAL/Condition/SwagTestConditionHandler.php",
                FileProviderInterface::LEGACY_DIR . "Components/SearchBundleDBAL/Facet/Facet.tpl" => "Components/SearchBundleDBAL/Facet/SwagTestFacet.php",
                FileProviderInterface::LEGACY_DIR . "Components/SearchBundleDBAL/Facet/FacetHandler.tpl" => "Components/SearchBundleDBAL/Facet/SwagTestFacetHandler.php",
                FileProviderInterface::LEGACY_DIR . "Components/SearchBundleDBAL/CriteriaRequestHandler.tpl" => "Components/SearchBundleDBAL/SwagTestCriteriaRequestHandler.php",
                FileProviderInterface::LEGACY_DIR . "Subscriber/SearchBundle.tpl" => "Subscriber/SearchBundle.php"
            ]
        ],
        'Frontend' => [
            'config' => 'hasFrontend',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Controllers/Frontend.tpl" => "Controllers/Frontend/SwagTest.php",
                FileProviderInterface::LEGACY_DIR . "Views/frontend/plugin_name/index.tpl" => "Views/frontend/swag_test/index.tpl"
            ]
        ],
        'Model' => [
            'config' => 'hasModels',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Models/Model.tpl" => "Models/SwagTest/Test.php",
                FileProviderInterface::LEGACY_DIR . "Models/Repository.tpl" => "Models/SwagTest/Repository.php"
            ]
        ],
        'Widget' => [
            'config' => 'hasWidget',
            'files' => [
                FileProviderInterface::LEGACY_DIR . "Views/backend/widget/main.tpl" => "Views/backend/swag_test/widgets/swag_test.js",
                FileProviderInterface::LEGACY_DIR . "Snippets/backend/widget/labels.tpl" => "Snippets/backend/widget/labels.ini"
            ]
        ],
        'ElasticSearch' => [
            'config' => 'hasElasticSearch',
            'files' => [
                FileProviderInterface::LEGACY_DIR . 'Subscriber/ORMBacklogSubscriber.php' => 'Subscriber/ORMBacklogSubscriber.php',
                FileProviderInterface::LEGACY_DIR . 'Components/ESIndexingBundle/Struct/Blog.tpl' => 'Components/ESIndexingBundle/Struct/Blog.php',
                FileProviderInterface::LEGACY_DIR . 'Components/ESIndexingBundle/BlogDataIndexer.tpl' => 'Components/ESIndexingBundle/BlogDataIndexer.php',
                FileProviderInterface::LEGACY_DIR . 'Components/ESIndexingBundle/BlogMapping.tpl' => 'Components/ESIndexingBundle/BlogMapping.php',
                FileProviderInterface::LEGACY_DIR . 'Components/ESIndexingBundle/BlogProvider.tpl' => 'Components/ESIndexingBundle/BlogProvider.php',
                FileProviderInterface::LEGACY_DIR . 'Components/ESIndexingBundle/BlogSettings.tpl' => 'Components/ESIndexingBundle/BlogSettings.php',
                FileProviderInterface::LEGACY_DIR . 'Components/ESIndexingBundle/BlogSynchronizer.tpl' => 'Components/ESIndexingBundle/BlogSynchronizer.php',
                FileProviderInterface::LEGACY_DIR . 'Components/SearchBundleES/BlogSearch.tpl' => 'Components/SearchBundleES/BlogSearch.php'
            ]
        ]
    ];

    /**
     * @return Configuration
     */
    private function getConfigObject()
    {
        $config = new Configuration();

        $config->hasBackend = false;
        $config->hasApi = false;
        $config->hasWidget = false;
        $config->hasFrontend = false;
        $config->hasCommands = false;
        $config->hasModels = false;
        $config->name = 'SwagTest';

        return $config;
    }

    /**
     * Foreach file provider: Create a plugin which needs this file provider and check,
     * if all required / pre-defined files actually exists.
     */
    public function testFileProvider()
    {
        $currentOutputDirectoryProvider = $this->getMockBuilder(CurrentOutputDirectoryProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $currentOutputDirectoryProvider->method('getPath')
            ->willReturn('');

        foreach ($this->fileProvider as $name => $provider) {
            $config = $this->getConfigObject();
            $configName = $provider['config'];
            $config->$configName = true;
            $config->backendModel = 'SwagTest\Models\Test';

            $this->providerTest($config, $provider, $this->fileProvider, $currentOutputDirectoryProvider);
        }
    }

    /**
     * Test each file provider with legacy files.
     */
    public function testLegacyFileProvider()
    {
        $legacyOutputDirectoryProvider = $this->getMockBuilder(LegacyOutputDirectoryProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $legacyOutputDirectoryProvider->method('getPath')
            ->willReturn('');

        foreach ($this->legacyFileProvider as $name => $provider) {
            $config = $this->getConfigObject();
            $config->isLegacyPlugin = true;
            $configName = $provider['config'];
            $config->$configName = true;
            $config->namespace = 'Frontend';

            $this->providerTest($config, $provider, $this->legacyFileProvider, $legacyOutputDirectoryProvider);
        }
    }

    /**
     * @param Configuration $config
     * @param array $provider
     * @param array $fileProviders
     * @param OutputDirectoryProviderInterface $outputDirectoryProvider
     */
    private function providerTest(
        Configuration $config,
        array $provider,
        array $fileProviders,
        OutputDirectoryProviderInterface $outputDirectoryProvider
    ) {
        $ioAdapter = new Dummy();
        $generator = new Generator(
            $ioAdapter,
            $config,
            new NameGenerator($config),
            new Template(),
            new LegacyOptionFileProviderLoader($config->isLegacyPlugin),
            $outputDirectoryProvider
        );

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
            array_column($fileProviders, 'files'),
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
