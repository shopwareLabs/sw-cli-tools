<?php

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;

/**
 * Class GitLab
 * @package Shopware\Plugin\Services\Repositories
 */
class GitLab extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPluginByName($name, $exact = false)
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            if (!$this->doesMatch($plugin->name, $name, $exact)) {
                unset($plugins[$key]);
            }
        }

        return $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        echo "Reading Gitlab org {$this->name}\n";

        $content = $this->restService->get($this->repository)->getResult();

        if (isset($content['errors'])) {
            throw new \RuntimeException(
                sprintf("Gitlab API communication error: '%s'", $content['errors'][0]['message'])
            );
        }

        $plugins = [];
        foreach ($content as $repo) {
            $plugins[] = $this->createPlugin($repo['ssh_url_to_repo'], $repo['http_url_to_repo'], $repo['name']);
        }

        return $plugins;
    }
}
