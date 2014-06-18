<?php

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;

class BitBucket extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPluginByName($name, $exact = false)
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            if (!$this->doesMatch($plugin->name, $name, $exact)) {
                unset ($plugins[$key]);
            }
        }

        return $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        echo "Reading BitBucket repo {$this->name}\n";
        $content = $this->restService->get($this->repository)->getResult();

        $plugins = array();
        foreach ($content['values'] as $repo) {
            $cloneUrls = array();

            foreach ($repo['links']['clone'] as $cloneUrl) {
                $cloneUrls[$cloneUrl['name']] = $cloneUrl['href'];
            }

            $cloneUrls['https'] = str_replace("shopwareAG@", "", $cloneUrls['https']);

            $plugins[] = $this->createPlugin($cloneUrls['ssh'], $cloneUrls['https'], $repo['name']);
        }

        return $plugins;
    }
}
