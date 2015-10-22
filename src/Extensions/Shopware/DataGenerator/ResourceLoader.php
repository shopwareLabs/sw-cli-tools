<?php

namespace Shopware\DataGenerator;

use Plugin\ShopwarePluginCreator\Generator;
use Shopware\DataGenerator\Resources\BaseResource;
use Symfony\Component\DependencyInjection\Container;

class ResourceLoader
{
    /**
     * @var string
     */
    private $assetDir;

    /**
     * @var BaseResource[]
     */
    private $resources;
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    public function __construct($assetDir, Container $container)
    {
        $this->assetDir = $assetDir;
        $this->container = $container;
    }

    /**
     * @param $type
     * @return BaseResource
     */
    public function getResource($type)
    {
        if (!isset($this->resources[$type])) {
            $className = 'Shopware\DataGenerator\Resources\\' . ucfirst($type);
            $this->resources[$type] = new $className(
                $this->assetDir,
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
