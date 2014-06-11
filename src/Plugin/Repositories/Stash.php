<?php

namespace ShopwareCli\Plugin\Repositories;

use ShopwareCli\Plugin\Factory;
use ShopwareCli\Struct\Plugin;
use ShopwareCli\Plugin\BaseRepository;

class Stash extends BaseRepository
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
        echo "Reading Stash repo {$this->name}\n";
        $content = $this->restService->get($this->repository);

        $plugins = array();
        foreach ($content['values'] as $repo) {
            $cloneUrl = $this->getCloneUrl($repo);
            $cloneUrl = str_replace("stashadmin@", "", $cloneUrl);

            $plugins[] = $this->createPlugin($cloneUrl,  $repo['name']);
        }

        return $plugins;
    }

    protected function getCloneUrl($repo)
    {
        $urls = array();

        foreach ($repo['links']['clone'] as $clone) {
            $urls[$clone['name']] = $clone['href'];
        }
        $get = $this->useHttp ? 'http' : 'ssh';

        if (!isset($urls[$get])) {
            throw new \RuntimeException("Could not clone via '{$get}");
        }

        return $urls[$get];

    }

}
