<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator;

use Shopware\DataGenerator\Struct\Config;

/**
 * Basically calls the resource classes and provides access to the options and random data generation method
 */
class DataGenerator
{
    /**
     * References the generator class
     *
     * @var RandomDataProvider
     */
    public $generator;
    /**
     * Type of demo data to create
     *
     * @var string
     */
    protected $type;

    /**
     * @var ResourceLoader
     */
    private $resourceLoader;

    /**
     * @var Struct\Config
     */
    private $config;

    /**
     * @param RandomDataProvider $generator
     * @param ResourceLoader     $resourceLoader
     * @param Config             $config
     */
    public function __construct(
        RandomDataProvider $generator,
        ResourceLoader $resourceLoader,
        Config $config
    ) {
        $this->generator = $generator;
        $this->resourceLoader = $resourceLoader;
        $this->config = $config;
    }

    /**
     * Triggers the actual sql creation methods of the resource
     *
     * For performance and memory reasons, different methods for data creation are provided
     */
    public function run()
    {
        $locale = $this->config->getGeneratorLocale();
        if (!empty($locale)) {
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

    /**
     * Init the random number generator with a specific seed.
     *
     * @param $seed
     */
    protected function initSeed($seed)
    {
        if (!empty($seed)) {
            mt_srand($this->config->getSeed());
        }
    }
}
