<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class WidgetFileProvider returns file for the ExtJS backend widget
 */
class WidgetFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasWidget) {
            return [];
        }

        return [
            self::CURRENT_DIR . 'Resources/views/backend/widget/main.tpl' => "Resources/views/backend/widgets/{$nameGenerator->under_score_js}.js",
            self::CURRENT_DIR . 'Subscriber/BackendWidget.tpl' => 'Subscriber/BackendWidget.php',
            self::CURRENT_DIR . 'Resources/snippets/backend/widget/labels.tpl' => 'Resources/snippets/backend/widget/labels.ini',
            self::CURRENT_DIR . 'Controllers/BackendWidget.tpl' => "Controllers/Backend/{$nameGenerator->backendWidgetController}.php",
        ];
    }
}
