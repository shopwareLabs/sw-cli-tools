<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;

class Stash extends BaseRepository
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
        echo "Reading Stash repo {$this->name}\n";

        $content = $this->restService->get($this->repository)->getResult();

        if (isset($content['errors'])) {
            throw new \RuntimeException(
                sprintf("Stash Repo communication error: '%s'", $content['errors'][0]['message'])
            );
        }

        $plugins = [];
        foreach ($content['values'] as $repo) {
            $urls = [];
            foreach ($repo['links']['clone'] as $clone) {
                $clone['href'] = str_replace('stashadmin@', '', $clone['href']);
                $urls[$clone['name']] = $clone['href'];
            }

            $plugins[] = $this->createPlugin($urls['ssh'], $urls['http'], $repo['name']);
        }

        return $plugins;
    }
}
