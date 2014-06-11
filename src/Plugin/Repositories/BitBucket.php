<?php

namespace ShopwareCli\Plugin\Repositories;

use ShopwareCli\Plugin\Factory;
use ShopwareCli\Struct\Plugin;
use ShopwareCli\Plugin\BaseRepository;

class BitBucket extends BaseRepository
{
    public function getPluginByName($name)
    {
        /** @var Plugin $plugin */
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            if (stripos($plugin->name, $name) === false) {
                unset ($plugins[$key]);
            }
        }

        return $plugins;
    }

    public function getPlugins()
    {
        echo "Reading BitBucket repo {$this->name}\n";
        $content = $this->restService->get($this->repository);

        $plugins = array();
        foreach ($content['values'] as $repo) {
            $cloneUrls = array();

            foreach ($repo['links']['clone'] as $cloneUrl) {
                $cloneUrls[$cloneUrl['name']] = $cloneUrl['href'];
            }
            $cloneUrl = $this->useHttp ? $cloneUrls['https'] : $cloneUrls['ssh'];

            $cloneUrl = str_replace("shopwareAG@", "", $cloneUrl);

            $plugins[] = $this->createPlugin($cloneUrl, $repo['name']);
        }

        return $plugins;
    }

}
