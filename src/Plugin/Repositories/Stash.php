<?php

namespace ShopwareCli\Plugin\Repositories;

use ShopwareCli\Struct\Plugin;
use ShopwareCli\Plugin\BaseRepository;

class Stash extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPluginByName($name)
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            if (stripos($plugin->name, $name) === false) {
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
        echo "Reading Stash repo {$this->name}\n";

        $content = $this->restService->get($this->repository);

        if (isset($content['errors'])) {
            throw new \RuntimeException(
                sprintf("Stash Repo communication error: '%s'", $content['errors'][0]['message'])
            );
        }

        $plugins = array();
        foreach ($content['values'] as $repo) {
            $cloneUrl = $this->getCloneUrl($repo);
            $cloneUrl = str_replace("stashadmin@", "", $cloneUrl);

            $plugins[] = $this->createPlugin($cloneUrl,  $repo['name']);
        }

        return $plugins;
    }

    /**
     * @param string $repo
     * @return string
     * @throws \RuntimeException
     */
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
