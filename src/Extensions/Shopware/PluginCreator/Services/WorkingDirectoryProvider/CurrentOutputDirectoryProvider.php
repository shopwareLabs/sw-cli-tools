<?php

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\RootDetectorInterface;

class CurrentOutputDirectoryProvider implements OutputDirectoryProviderInterface
{
    const CURRENT_PLUGIN_DIR = 'custom/plugins';

    /**
     * @var RootDetectorInterface
     */
    private $rootDetector;

    /**
     * @var string
     */
    private $name;

    /**
     * @param RootDetectorInterface $rootDetector
     * @param string $name
     */
    public function __construct(RootDetectorInterface $rootDetector, $name)
    {
        $this->rootDetector = $rootDetector;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if ($this->rootDetector->isRoot($this->getCwd())) {
            return $this->getCwd().'/'.self::CURRENT_PLUGIN_DIR.'/'.$this->name.'/';
        }
        return $this->getCwd().'/'.$this->name.'/';
    }

    /**
     * @return string
     */
    private function getCwd()
    {
        return getcwd();
    }
}
