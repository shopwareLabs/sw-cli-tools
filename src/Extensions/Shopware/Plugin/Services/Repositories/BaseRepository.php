<?php
declare(strict_types=1);
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories;

use Shopware\Plugin\Services\PluginFactory;
use Shopware\Plugin\Struct\Plugin;
use ShopwareCli\Services\Rest\RestInterface;

/**
 * Base repository class providing a constructor for injection and a convenient access to the PluginFactory
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected string $name;

    protected string $repository;

    protected ?RestInterface $restService;

    public function __construct(string $repository, string $name, ?RestInterface $restService = null)
    {
        $this->repository = $repository;
        $this->name = $name;
        $this->restService = $restService;
    }

    public function createPlugin(string $sshUrl, string $httpUrl, string $name): Plugin
    {
        $type = \array_slice(\explode('\\', \get_class($this)), -1);
        $type = $type[0];
        $name = \str_replace(' ', '', $name);

        return PluginFactory::getPlugin($name, $sshUrl, $httpUrl, $this->name, $type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Very simple compare method
     */
    protected function doesMatch(string $actual, string $searched, bool $exact = false): bool
    {
        return !$exact && \stripos($actual, $searched) !== false
            || $exact && $searched == $actual
        ;
    }
}
