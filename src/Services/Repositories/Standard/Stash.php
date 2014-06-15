<?php

namespace ShopwareCli\Services\Repositories\Standard;

use ShopwareCli\Services\Repositories\BaseRepository;

/**
 * Class Stash
 * @package ShopwareCli\Services\Repositories
 */
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
            $urls = array();
            foreach ($repo['links']['clone'] as $clone) {
                $clone['href'] = str_replace("stashadmin@", "", $clone['href']);
                $urls[$clone['name']] = $clone['href'];
            }

            $plugins[] = $this->createPlugin($urls['ssh'], $urls['http'],  $repo['name']);
        }

        return $plugins;
    }
}
