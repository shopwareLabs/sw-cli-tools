<?php

namespace Shopware\PluginCreator\Services\TemplateFileProvider;

use Shopware\PluginCreator\Services\NameGenerator;
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

        if ($configuration->isLegacyPlugin) {
            return $this->getLegacyFiles($nameGenerator);
        }

        return [
            self::CURRENT_DIR . "Resources/views/backend/application/app.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/app.js",
            self::CURRENT_DIR . "Resources/views/backend/application/controller/main.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/controller/main.js",
            self::CURRENT_DIR . "Resources/views/backend/application/model/main.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/model/main.js",
            self::CURRENT_DIR . "Resources/views/backend/application/store/main.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/store/main.js",
            self::CURRENT_DIR . "Resources/views/backend/application/view/detail/container.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/view/detail/container.js",
            self::CURRENT_DIR . "Resources/views/backend/application/view/detail/window.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/view/detail/window.js",
            self::CURRENT_DIR . "Resources/views/backend/application/view/list/list.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/view/list/list.js",
            self::CURRENT_DIR . "Resources/views/backend/application/view/list/window.tpl" => "Resources/views/backend/{$nameGenerator->under_score_js}/view/list/window.js",
        ];
    }

    /**
     * @param NameGenerator $nameGenerator
     * @return array
     */
    private function getLegacyFiles(NameGenerator $nameGenerator)
    {
        return [
            self::LEGACY_DIR . "Views/backend/application/app.tpl" => "Views/backend/{$nameGenerator->under_score_js}/app.js",
            self::LEGACY_DIR . "Views/backend/application/controller/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/controller/main.js",
            self::LEGACY_DIR . "Views/backend/application/model/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/model/main.js",
            self::LEGACY_DIR . "Views/backend/application/store/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/store/main.js",
            self::LEGACY_DIR . "Views/backend/application/view/detail/container.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/detail/container.js",
            self::LEGACY_DIR . "Views/backend/application/view/detail/window.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/detail/window.js",
            self::LEGACY_DIR . "Views/backend/application/view/list/list.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/list/list.js",
            self::LEGACY_DIR . "Views/backend/application/view/list/window.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/list/window.js",
        ];
    }
}
