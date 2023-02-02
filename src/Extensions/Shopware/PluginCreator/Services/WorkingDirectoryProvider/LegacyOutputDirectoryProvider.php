<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider;

use Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector\RootDetectorInterface;

class LegacyOutputDirectoryProvider implements OutputDirectoryProviderInterface
{
    public const FRONTEND_NAMESPACE = 'Frontend';
    private const LEGACY_PLUGIN_DIR = 'engine/Shopware/Plugins/Local';
    private const BACKEND_NAMESPACE = 'Backend';
    private const CORE_NAMESPACE = 'Core';

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var RootDetectorInterface
     */
    private $rootDetector;

    private $name;

    /**
     * @param string $name
     * @param string $namespace
     *
     * @throws \Exception
     */
    public function __construct(RootDetectorInterface $rootDetector, $name, $namespace)
    {
        if (!$this->isValidNamespace($namespace) && $namespace !== '') {
            throw new \Exception(
                \sprintf('Invalid namespace given: %s', $namespace)
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
            return $this->getCwd() . '/' . self::LEGACY_PLUGIN_DIR . '/' . $this->namespace . '/' . $this->name . '/';
        }

        return $this->getCwd() . '/' . $this->name . '/';
    }

    /**
     * @param string $namespace
     */
    private function isValidNamespace($namespace): bool
    {
        return \in_array($namespace, [self::FRONTEND_NAMESPACE, self::BACKEND_NAMESPACE, self::CORE_NAMESPACE], true);
    }

    private function getCwd(): string
    {
        return \getcwd();
    }
}
