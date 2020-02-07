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
 * Class GitLab
 */
class GitLab extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPluginByName($name, $exact = false): array
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
        echo "Reading Gitlab org {$this->name}\n";

        $content = $this->restService->get($this->repository)->getResult();

        if (isset($content['errors'])) {
            throw new \RuntimeException(
                sprintf("Gitlab API communication error: '%s'", $content['errors'][0]['message'])
            );
        }

        $plugins = [];
        foreach ($content as $repo) {
            $pluginName = $repo['name'];

            $splitPath = explode('_', $repo['path']);
            if (\count($splitPath) === 2) {
                $pluginName = ucfirst($splitPath[0]) . '_' . $pluginName;
            }

            $plugins[] = $this->createPlugin($repo['ssh_url_to_repo'], $repo['http_url_to_repo'], $pluginName);
        }

        return $plugins;
    }
}
