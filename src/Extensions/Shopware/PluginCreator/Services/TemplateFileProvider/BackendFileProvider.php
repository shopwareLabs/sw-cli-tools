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

        return array(
            "Views/backend/application/app.tpl" => "Views/backend/{$nameGenerator->under_score_js}/app.js",
            "Views/backend/application/controller/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/controller/main.js",
            "Views/backend/application/model/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/model/main.js",
            "Views/backend/application/store/main.tpl" => "Views/backend/{$nameGenerator->under_score_js}/store/main.js",
            "Views/backend/application/view/detail/container.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/detail/container.js",
            "Views/backend/application/view/detail/window.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/detail/window.js",
            "Views/backend/application/view/list/list.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/list/list.js",
            "Views/backend/application/view/list/window.tpl" => "Views/backend/{$nameGenerator->under_score_js}/view/list/window.js",
        );
    }

}