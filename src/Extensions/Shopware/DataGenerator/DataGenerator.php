<?php

namespace Shopware\DataGenerator;

use Shopware\DataGenerator\RandomDataProvider;
use Shopware\DataGenerator\Resources;
use Shopware\DataGenerator\Struct\Config;
use ShopwareCli\Services\IoService;

/**
 * Basically calls the resource classes and provides access to the options and random data generation method
 */
class DataGenerator
{
    /**
     * Type of demo data to create
     *
     * @var string
     */
    protected $type;

    /**
     * References the generator class
     * @var RandomDataProvider
     */
    public $generator;

    /**
     * @var ResourceLoader
     */
    private $resourceLoader;

    /**
     * @var Struct\Config
     */
    private $config;

    /**
     * @var IoService
     */
    private $ioService;

    /**
     * @param RandomDataProvider $generator
     * @param ResourceLoader $resourceLoader
     * @param Config $config
     * @param IoService $ioService
     */
    public function __construct(
        RandomDataProvider $generator,
        ResourceLoader $resourceLoader,
        Config $config,
        IoService $ioService
    ) {
        $this->generator = $generator;
        $this->resourceLoader = $resourceLoader;
        $this->config = $config;
        $this->ioService = $ioService;
    }

    /**
     * Init the random number generator with a specific seed.
     * @param $seed
     */
    protected function initSeed($seed)
    {
        if (!empty($seed)) {
            srand($this->config->getSeed());
        }
    }

    /**
     * Triggers the actual sql creation methods of the resource
     *
     * For performance and memory reasons, different methods for data creation are provided
     *
     */
    public function run()
    {
        $locale = $this->config->getGeneratorLocale();
        if(!empty($locale)){
            $this->generator->setProviderLocale($locale);
        }

        $this->initSeed($this->config->getSeed());
        $this->config->createOutputDir();

        $resource = $this->resourceLoader->getResource($this->type);

        $resource->generateData();
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
