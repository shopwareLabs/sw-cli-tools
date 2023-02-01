<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest;

use ShopwareCli\Services\Rest\Curl\RestClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Factory for cache decorated rest services
 */
class RestServiceFactory
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function factory(
        string $baseUrl,
        ?string $username = null,
        ?string $password = null,
        int $cacheTime = 3600
    ): RestInterface {
        return new CacheDecorator(
            new RestClient($baseUrl, $username, $password),
            $this->container->get('cache'),
            $cacheTime
        );
    }
}
