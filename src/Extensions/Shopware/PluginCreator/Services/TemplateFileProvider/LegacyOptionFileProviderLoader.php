<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

class LegacyOptionFileProviderLoader implements FileProviderLoaderInterface
{
    /**
     * @var bool
     */
    private $isLegacy;

    /**
     * @param bool $isLegacy
     *
     * @throws \Exception
     */
    public function __construct($isLegacy)
    {
        if (!\is_bool($isLegacy)) {
            throw new \Exception('Boolean expected, got ' . \gettype($isLegacy));
        }

        $this->isLegacy = $isLegacy;
    }

    /**
     * Loads all file providers based on the --legacy option.
     *
     * @return FileProviderInterface[]
     */
    public function load()
    {
        if ($this->isLegacy) {
            return $this->getLegacyProvider();
        }

        return $this->getCurrentProvider();
    }

    /**
     * @return FileProviderInterface[]
     */
    private function getLegacyProvider(): array
    {
        return [
            new Legacy\ApiFileProvider(),
            new Legacy\BackendControllerFileProvider(),
            new Legacy\BackendFileProvider(),
            new Legacy\CommandFileProvider(),
            new Legacy\ControllerPathFileProvider(),
            new Legacy\DefaultFileProvider(),
            new Legacy\FilterFileProvider(),
            new Legacy\FrontendFileProvider(),
            new Legacy\ModelFileProvider(),
            new Legacy\WidgetFileProvider(),
            new Legacy\ElasticSearchProvider(),
        ];
    }

    /**
     * @return FileProviderInterface[]
     */
    private function getCurrentProvider(): array
    {
        return [
            new Current\ApiFileProvider(),
            new Current\BackendControllerFileProvider(),
            new Current\BackendFileProvider(),
            new Current\CommandFileProvider(),
            new Current\ControllerPathFileProvider(),
            new Current\DefaultFileProvider(),
            new Current\FilterFileProvider(),
            new Current\FrontendFileProvider(),
            new Current\ModelFileProvider(),
            new Current\WidgetFileProvider(),
            new Current\ElasticSearchProvider(),
        ];
    }
}
