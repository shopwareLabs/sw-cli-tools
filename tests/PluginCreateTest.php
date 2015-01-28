<?php

namespace ShopwareCli\Tests;

use Shopware\PluginCreator\Services\Generator;
use Shopware\PluginCreator\Services\IoAdapter\Dummy;
use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\Template;
use Shopware\PluginCreator\Struct\Configuration;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class PluginCreateTest extends \PHPUnit_Framework_TestCase
{

    protected $defaultFiles = array(
        'Bootstrap.php',
        'Readme.md',
        'plugin.json',
        'Subscriber/Frontend.php',
        'phpunit.xml.dist',
        'tests/Test.php'
    );

    protected $backendFiles = array(
        'Views/backend/swag_test/model/main.js',
        'Views/backend/swag_test/store/main.js',
        'Views/backend/swag_test/view/detail/container.js',
        'Views/backend/swag_test/view/detail/window.js',
        'Views/backend/swag_test/view/list/list.js',
        'Views/backend/swag_test/view/list/window.js',
        'Views/backend/swag_test/app.js',
        'Controllers/Backend/SwagTest.php',
        'Subscriber/ControllerPath.php',
    );

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
     * Test the simplest plugin config
     */
    public function testCreateSimplePlugin()
    {
        $config = $this->getConfigObject();
        $ioAdapter = new Dummy();
        $generator = new Generator($ioAdapter, $config, new NameGenerator($config), new Template());
        $generator->setOutputDirectory('');

        $generator->run();


        foreach ($this->defaultFiles as $file) {
            $this->assertTrue(
                in_array($file, array_keys($ioAdapter->getFiles())),
                "{$file} not found in generated files"
            );
        }
    }

    /**
     * Test a plugin with backend files
     */
    public function testBackendPlugin()
    {
        $config = $this->getConfigObject();
        $config->hasBackend = true;
        $config->backendModel = 'Shopware\CustomModels\SwagTest\Test';

        $ioAdapter = new Dummy();
        $generator = new Generator($ioAdapter, $config, new NameGenerator($config), new Template());
        $generator->setOutputDirectory('');

        $generator->run();

        foreach (array_merge($this->defaultFiles, $this->backendFiles) as  $file) {
            $this->assertTrue(
                in_array($file, array_keys($ioAdapter->getFiles())),
                "{$file} not found in generated files"
            );
        }
    }
}
