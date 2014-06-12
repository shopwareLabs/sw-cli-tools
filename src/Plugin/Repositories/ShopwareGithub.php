<?php

namespace ShopwareCli\Plugin\Repositories;

use ShopwareCli\Plugin\BaseRepository;

/**
 * This currently only supports a finite list of shopware plugins on github, as currently the
 * repository names on github are not compliant with the shopwareCli tools
 *
 * Class ShopwareGithub
 * @package ShopwareCli\Plugin\Repositories
 */
class ShopwareGithub extends BaseRepository
{
    protected $mapping = array(
        'sw4-premium-swagliveshopping' => 'Frontend_SwagLiveShopping',
        'sw4-premium-swagcustomizing' => 'Frontend_SwagCustomizing',
        'sw4-premium-swagbundle' => 'Frontend_SwagBundle',
        'sw4-premium-swagwizard' => 'Frontend_SwagWizard',
        'sw4-premium-swagmultiedit' => 'Backend_SwagMultiEdit',
        'sw4-premium-swagbonussystem' => 'Frontend_SwagBonusSystem',
        'sw4-premium-swagticketsystem' => 'Core_SwagTicketSystem',
        'sw4-premium-swagstaging' => 'Core_SwagStaging',
        'sw4-premium-swagfuzzy' => 'Backend_SwagFuzzy',
        'sw4-premium-swagnewsletter' => 'Backend_SwagNewsletter',
        'sw4-premium-swagbusinessessentials' => 'Backend_SwagBusinessEssentials',
        'sw4-premium-swagabocommerce' => 'Frontend_SwagAboCommerce',
        'FactFinder' => 'Frontend_OmecoFactFinder',
    );

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
        echo "Reading Shopware repo {$this->name}\n";
        $content = $this->restService->get($this->repository);

        $knownRepos = array_keys($this->mapping);

        $plugins = array();
        foreach ($content as $repo) {
            if (!in_array($repo['name'], $knownRepos)) {
                continue;
            }

            // Map repo name
            $repo['name'] = $this->mapping[$repo['name']];
            $cloneUrl = $this->useHttp ? $repo['clone_url'] : $repo['ssh_url'];

            $plugins[] = $this->createPlugin($cloneUrl, $repo['name']);
        }

        return $plugins;
    }
}
