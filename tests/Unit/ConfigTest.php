<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ShopwareCli\Config;
use ShopwareCli\ConfigFileCollector;

class ConfigTest extends TestCase
{
    public function testItCanBeCreated(): void
    {
        $config = new Config(new ConfigFileCollectorDummy());
        static::assertInstanceOf(Config::class, $config);
    }

    public function testItShouldCreateConfigFromSingleFile(): void
    {
        $config = new Config(new SingleConfigFileCollectorMock());

        static::assertTrue($config->offsetExists('test'));
        static::assertEquals(
            ['some_config' => 'some_value'],
            $config->offsetGet('test')
        );
    }

    public function testItShouldCreateConfigFromMultipleFiles(): void
    {
        $config = new Config(new MultiConfigFileCollectorMock());

        static::assertEquals(
            ['some_config1' => 'some_value1'],
            $config->offsetGet('config1')
        );
        static::assertEquals(
            ['some_config2' => 'some_value2'],
            $config->offsetGet('config2')
        );
    }

    public function testItShouldOverrideFirstConfigFile(): void
    {
        $config = new Config(new OverrideConfigFileCollectorMock());

        static::assertFileExists(__DIR__ . '/_fixtures/override_config1.yml');
        static::assertEquals(
            ['some_config' => 'override'],
            $config->offsetGet('config')
        );
    }

    public function testItShouldMergeConfigFiles(): void
    {
        $config = new Config(new MergeConfigFileCollectorMock());

        static::assertEquals(
            ['some_config' => 'some_value', 'merged_config' => 'merged_value'],
            $config->offsetGet('config')
        );
    }

    public function testItShouldReplaceAndMergeConfigsRecursive(): void
    {
        $config = new Config(new ExtendConfigFileCollectorMock());

        static::assertEquals([
            'some_config' => 'override',
            'extend_config' => 'extend',
            'recursive' => [
                'config1' => 'value1',
                'config2' => 'override',
                'config3' => 'value3',
                'not_existing' => 'value',
            ],
        ], $config->offsetGet('config'));
    }
}

class ConfigFileCollectorDummy extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles(): array
    {
        return [];
    }
}

class SingleConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles(): array
    {
        return [
            __DIR__ . '/_fixtures/single_config.yml',
        ];
    }
}

class MultiConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles(): array
    {
        return [
            __DIR__ . '/_fixtures/multi_config1.yml',
            __DIR__ . '/_fixtures/multi_config2.yml',
        ];
    }
}

class OverrideConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles(): array
    {
        return [
            __DIR__ . '/_fixtures/override_config1.yml',
            __DIR__ . '/_fixtures/override_config2.yml',
        ];
    }
}

class MergeConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles(): array
    {
        return [
            __DIR__ . '/_fixtures/merge_config1.yml',
            __DIR__ . '/_fixtures/merge_config2.yml',
        ];
    }
}

class ExtendConfigFileCollectorMock extends ConfigFileCollector
{
    public function __construct()
    {
    }

    public function collectConfigFiles(): array
    {
        return [
            __DIR__ . '/_fixtures/extend_config1.yml',
            __DIR__ . '/_fixtures/extend_config2.yml',
        ];
    }
}
