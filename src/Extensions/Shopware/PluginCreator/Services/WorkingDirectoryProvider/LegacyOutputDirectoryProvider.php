<?php

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\RootDetectorInterface;

class LegacyOutputDirectoryProvider implements OutputDirectoryProviderInterface
{
    const LEGACY_PLUGIN_DIR = 'engine/Shopware/Plugins/Local';

    const FRONTEND_NAMESPACE = 'Frontend';
    const BACKEND_NAMESPACE = 'Backend';
    const CORE_NAMESPACE = 'Core';

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var RootDetectorInterface
     */
    private $rootDetector;

    /**
     * @var
     */
    private $name;

    /**
     * @param RootDetectorInterface $rootDetector
     * @param string $name
     * @param string $namespace
     *
     * @throws \Exception
     */
    public function __construct(RootDetectorInterface $rootDetector, $name, $namespace)
    {
        if (!$this->isValidNamespace($namespace) && strlen($namespace)) {
            throw new \Exception(
                sprintf('Invalid namespace given: %s', $namespace)
            );
        }

        $this->namespace = $namespace;
        $this->rootDetector = $rootDetector;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if ($this->rootDetector->isRoot($this->getCwd())) {
            return $this->getCwd().'/'.self::LEGACY_PLUGIN_DIR.'/'.$this->namespace.'/'.$this->name.'/';
        }

        return $this->getCwd().'/'.$this->name.'/';
    }

    /**
     * @param string $namespace
     *
     * @return bool
     */
    private function isValidNamespace($namespace)
    {
        return self::FRONTEND_NAMESPACE == $namespace || self::BACKEND_NAMESPACE == $namespace || self::CORE_NAMESPACE == $namespace;
    }

    /**
     * @return string
     */
    private function getCwd()
    {
        return getcwd();
    }
}
