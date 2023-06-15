<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;

class GitHub extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPluginByName(string $name, bool $exact = false): array
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
    public function getPlugins(): array
    {
        echo "Reading Shopware repo {$this->name}\n";
        $content = $this->restService->get($this->repository)->getResult();
        if (!\array_key_exists('items', $content)) {
            return [];
        }

        $plugins = [];
        foreach ($content['items'] as $repo) {
            $plugins[] = $this->createPlugin($repo['ssh_url'], $repo['clone_url'], $repo['name']);
        }

        return $plugins;
    }
}
