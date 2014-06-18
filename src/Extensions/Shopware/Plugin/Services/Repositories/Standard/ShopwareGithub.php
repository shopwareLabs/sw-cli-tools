<?php

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;

/**
 * This currently only supports a finite list of shopware plugins on github, as currently the
 * repository names on github are not compliant with the shopwareCli tools
 *
 * This could also be replaces with a SimpleList having all the following repositories in the config.yaml
 *
 * Class ShopwareGithub
 * @package Shopware\Plugin\Services\Repositories
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
        echo "Reading Shopware repo {$this->name}\n";
        $content = $this->restService->get($this->repository)->getResult();

        $knownRepos = array_keys($this->mapping);

        $plugins = array();
        foreach ($content as $repo) {
            if (!in_array($repo['name'], $knownRepos)) {
                continue;
            }

            // Map repo name
            $repo['name'] = $this->mapping[$repo['name']];

            $plugins[] = $this->createPlugin($repo['ssh_url'], $repo['clone_url'], $repo['name']);
        }

        return $plugins;
    }
}
