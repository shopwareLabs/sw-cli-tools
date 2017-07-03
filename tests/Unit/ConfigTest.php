<?php

namespace ShopwareCli\Tests\Unit;

use ShopwareCli\Config;
use ShopwareCli\ConfigFileCollector;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_be_created()
    {
        $config = new Config(new ConfigFileCollectorDummy());
        $this->assertInstanceOf(Config::class, $config);
    }

    public function test_it_should_create_config_from_single_file()
    {
        $config = new Config(new SingleConfigFileCollectorMock());

        $this->assertTrue($config->offsetExists('test'));
        $this->assertEquals(
            [ 'some_config' => 'some_value' ],
            $config->offsetGet('test')
        );
    }

    public function test_it_should_create_config_from_multiple_files()
    {
        $config = new Config(new MultiConfigFileCollectorMock());

        $this->assertEquals(
            [ 'some_config1' => 'some_value1' ],
            $config->offsetGet('config1')
        );
        $this->assertEquals(
            [ 'some_config2' => 'some_value2' ],
            $config->offsetGet('config2')
        );
    }

    public function test_it_should_override_first_config_file()
    {
        $config = new Config(new OverrideConfigFileCollectorMock());

        $this->assertFileExists(__DIR__ . '/_fixtures/override_config1.yml');
        $this->assertEquals(
            [ 'some_config' => 'override' ],
            $config->offsetGet('config')
        );
    }

    public function test_it_should_merge_config_files()
    {
        $config = new Config(new MergeConfigFileCollectorMock());

        $this->assertEquals(
            ['some_config' => 'some_value', 'merged_config' => 'merged_value'],
            $config->offsetGet('config')
        );
    }

    public function test_it_should_replace_and_merge_configs_recursive()
    {
        $config = new Config(new ExtendConfigFileCollectorMock());

        $this->assertEquals([
            'some_config' => 'override',
            'extend_config' => 'extend',
            'recursive' => [
                'config1' => 'value1',
                'config2' => 'override',
                'config3' => 'value3',
                'not_existing' => 'value'
            ]
        ], $config->offsetGet('config'));
    }
}

class ConfigFileCollectorDummy extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles()
    {
        return [];
    }
}

class SingleConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles()
    {
        return [
            __DIR__ . '/_fixtures/single_config.yml'
        ];
    }
}

class MultiConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles()
    {
        return [
            __DIR__ . '/_fixtures/multi_config1.yml',
            __DIR__ . '/_fixtures/multi_config2.yml'
        ];
    }
}

class OverrideConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles()
    {
        return [
            __DIR__ . '/_fixtures/override_config1.yml',
            __DIR__ . '/_fixtures/override_config2.yml'
        ];
    }
}

class MergeConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles()
    {
        return [
            __DIR__ . '/_fixtures/merge_config1.yml',
            __DIR__ . '/_fixtures/merge_config2.yml'
        ];
    }
}

class ExtendConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles()
    {
        return [
            __DIR__ . '/_fixtures/extend_config1.yml',
            __DIR__ . '/_fixtures/extend_config2.yml'
        ];
    }
}
