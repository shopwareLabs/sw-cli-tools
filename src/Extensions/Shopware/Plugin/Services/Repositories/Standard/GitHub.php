<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;

/**
 * Class GitHub
 */
class GitHub extends BaseRepository
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
        echo "Reading Shopware repo {$this->name}\n";
        $content = $this->restService->get($this->repository)->getResult();

        $plugins = [];
        foreach ($content as $repo) {
            $plugins[] = $this->createPlugin($repo['ssh_url'], $repo['clone_url'], $repo['name']);
        }

        return $plugins;
    }
}
