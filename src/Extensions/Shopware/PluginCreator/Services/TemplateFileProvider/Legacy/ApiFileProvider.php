<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class ApiFileProvider returns files required for the API
 */
class ApiFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasApi) {
            return [];
        }

        return [
            self::LEGACY_DIR . 'Components/Api/Resource/Resource.tpl' => "Components/Api/Resource/{$nameGenerator->camelCaseModel}.php",
            self::LEGACY_DIR . 'Controllers/Api.tpl' => "Controllers/Api/{$nameGenerator->camelCaseModel}.php",
        ];
    }
}
