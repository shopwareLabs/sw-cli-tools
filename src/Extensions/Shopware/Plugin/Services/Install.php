<?php

namespace Shopware\Plugin\Services;

use ShopwareCli\Services\IoService;
use Shopware\Plugin\Struct\Plugin;

/**
 * Checks out a given plugin, activates it and adds it to the phpstorm vcs.xml
 *
 * Class Install
 * @package Shopware\Plugin\Services
 */
class Install
{
    /**
     * @var Checkout
     */
    protected $checkout
    ;
    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @param Checkout  $checkout
     * @param IoService $ioService
     */
    public function __construct(Checkout $checkout, IoService $ioService)
    {
        $this->checkout = $checkout;
        $this->ioService = $ioService;
    }

    /**
     * @param Plugin $plugin
     * @param string $shopwarePath
     * @param bool   $inputActivate
     * @param string $branch
     * @param bool   $useHttp
     */
    public function install(Plugin $plugin, $shopwarePath, $inputActivate = false, $branch = 'master', $useHttp = false)
    {
        $pluginName = $plugin->name;

        $this->checkout->checkout($plugin, $shopwarePath . '/engine/Shopware/Plugins/Local/', $branch, $useHttp);

        if ($inputActivate) {
            $this->ioService->writeln(exec($shopwarePath . '/bin/console sw:plugin:refresh'));
            $this->ioService->writeln(exec($shopwarePath . '/bin/console sw:plugin:install --activate ' . $pluginName));
        }

        $this->addPluginVcsMapping($plugin, $shopwarePath);

        return;
    }

    /**
     * @param Plugin $plugin
     * @param string $shopwarePath
     */
    public function addPluginVcsMapping(Plugin $plugin, $shopwarePath)
    {
        $vcsMappingFile = $shopwarePath . '/.idea/vcs.xml';
        $pluginDestPath = $plugin->module . "/" . $plugin->name;

        if (!file_exists($vcsMappingFile)) {
            return;
        }

        $mapping = file_get_contents($vcsMappingFile);
        $xml = new \SimpleXMLElement($mapping);
        foreach ($xml->component->mapping as $mapping) {
            // if already mapped, return
            if (strpos($this->normalize($mapping['directory']), $this->normalize($pluginDestPath)) !== false) {
                return;
            }
        }

        $mappingDirectory = '$PROJECT_DIR$/engine/Shopware/Plugins/Local/' . $pluginDestPath;

        // mapping needs to be created
        $newMapping = $xml->component->addChild('mapping');
        $newMapping->addAttribute('vcs', 'Git');
        $newMapping->addAttribute('directory', $mappingDirectory);

        $xml->asXML($vcsMappingFile);
    }

    /**
     * Normalize directory strings to make them comparable
     *
     * @param $string
     * @return string
     */
    private function normalize($string)
    {
        return strtolower(str_replace(array('/', '\\'), '-', $string));
    }
}
