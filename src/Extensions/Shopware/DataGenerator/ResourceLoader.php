<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator;

use Shopware\DataGenerator\Resources\BaseResource;
use Symfony\Component\DependencyInjection\Container;

class ResourceLoader
{
    /**
     * @var BaseResource[]
     */
    private $resources;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getResource($type): BaseResource
    {
        if (!isset($this->resources[$type])) {
            $className = 'Shopware\DataGenerator\Resources\\' . ucfirst($type);
            $this->resources[$type] = new $className(
                $this->container->get('generator_config'),
                $this->container->get('random_data_provider'),
                $this->container->get('io_service'),
                $this->container->get('writer_manager')
            );

            if (strtolower($type) == 'orders') {
                $this->resources[$type]->setArticleResource($this->getResource('articles'));
            }
            if (strtolower($type) == 'articles') {
                $this->resources[$type]->setCategoryResource($this->getResource('categories'));
            }
        }

        return $this->resources[$type];
    }
}
