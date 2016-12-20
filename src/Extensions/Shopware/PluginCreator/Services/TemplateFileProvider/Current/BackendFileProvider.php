<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class BackendFileProvider returns ExtJS backend files
 * @package Shopware\PluginCreator\Services\TemplateFileProvider
 */
class BackendFileProvider implements FileProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasBackend) {
            return [];
        }

        return [
            self::CURRENT_DIR . 'Resources/views/backend/application/app.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/app.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/controller/main.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/controller/main.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/model/main.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/model/main.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/store/main.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/store/main.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/view/detail/container.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/view/detail/container.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/view/detail/window.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/view/detail/window.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/view/list/list.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/view/list/list.js",
            self::CURRENT_DIR . 'Resources/views/backend/application/view/list/window.tpl' => "Resources/views/backend/{$nameGenerator->under_score_js}/view/list/window.js",
        ];
    }
}
