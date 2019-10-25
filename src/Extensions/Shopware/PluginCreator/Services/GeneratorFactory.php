<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services;

use Shopware\PluginCreator\Services\IoAdapter\HardDrive;
use Shopware\PluginCreator\Services\TemplateFileProvider\LegacyOptionFileProviderLoader;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\CurrentOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\LegacyOutputDirectoryProvider;
use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\ShopwareRootDetector;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class GeneratorFactory
 */
class GeneratorFactory
{
    /**
     * @param Configuration $configuration
     *
     * @return Generator
     */
    public function create(Configuration $configuration)
    {
        $legacyOptionFileProvider = new LegacyOptionFileProviderLoader($configuration->isLegacyPlugin);
        $outputDirectoryProvider = $this->getOutputDirectoryProvider($configuration);

        return new Generator(
            new HardDrive(),
            $configuration,
            new NameGenerator($configuration),
            new Template(),
            $legacyOptionFileProvider,
            $outputDirectoryProvider
        );
    }

    /**
     * @param Configuration $configuration
     *
     * @return CurrentOutputDirectoryProvider|LegacyOutputDirectoryProvider
     */
    private function getOutputDirectoryProvider(Configuration $configuration)
    {
        $outputDirectoryProvider = new CurrentOutputDirectoryProvider(
            new ShopwareRootDetector(),
            $configuration->name
        );

        if ($configuration->isLegacyPlugin) {
            return new LegacyOutputDirectoryProvider(
                new ShopwareRootDetector(),
                $configuration->name,
                $configuration->namespace
            );
        }

        return $outputDirectoryProvider;
    }
}
