<?php

namespace Shopware\DataGenerator\Struct;

class Config
{
    /**
     * Default values.
     *
     * @var
     */
    protected $numberArticles = 0;

    protected $numberCategories = 0;

    protected $numberCustomers = 0;

    protected $numberOrders = 0;

    protected $numberNewsletter = 0;

    protected $numberVouchers = 0;

    protected $categoriesPerArticle = 0;

    protected $articleFilterGroups = 0;

    protected $articleFilterOptions = 0;

    protected $articleFilterValues = 0;

    /**
     * Path of the image directory.
     *
     * @var string
     */
    protected $imageDir;

    /**
     * @var
     */
    protected $createImages;

    /**
     * @var mixed
     */
    protected $thumbnailSizes;

    /**
     * Number of variants each configurator article will have at least.
     *
     * @var int
     */
    protected $minVariants;

    /**
     * Number of variants each configurator article will have at most.
     *
     * @var int
     */
    protected $maxVariants;

    /**
     * Output file for the sql file.
     *
     * @var string
     */
    protected $outputName;

    /**
     * Seed used to init the random generator
     * Using a default seed here, so usually you will get reproducible random numbers.
     *
     * @var int
     */
    protected $seed;

    /**
     * Number of values per insert.
     *
     * @var int
     */
    protected $chunkSize;

    /**
     * @param $numberArticles
     */
    public function setNumberArticles($numberArticles)
    {
        $this->numberArticles = $numberArticles;
    }

    /**
     * @return int
     */
    public function getNumberArticles()
    {
        return $this->numberArticles;
    }

    /**
     * @param int $numberCategories
     */
    public function setNumberCategories($numberCategories)
    {
        $this->numberCategories = $numberCategories;
    }

    /**
     * @param int $categoriesPerArticle
     */
    public function setNumberCategoriesPerArticle($categoriesPerArticle)
    {
        $this->categoriesPerArticle = max(1, $categoriesPerArticle);
    }

    /**
     * @return int
     */
    public function getCategoriesPerArticle()
    {
        return $this->categoriesPerArticle;
    }

    /**
     * @return int
     */
    public function getNumberCategories()
    {
        return $this->numberCategories;
    }

    /**
     * @param $numberCustomers
     */
    public function setNumberCustomers($numberCustomers)
    {
        $this->numberCustomers = $numberCustomers;
    }

    /**
     * @return int
     */
    public function getNumberCustomers()
    {
        return $this->numberCustomers;
    }

    /**
     * @param $numberOrders
     */
    public function setNumberOrders($numberOrders)
    {
        $this->numberOrders = $numberOrders;
    }

    /**
     * @return int
     */
    public function getNumberOrders()
    {
        return $this->numberOrders;
    }

    /**
     * @param $numberNewsletter
     */
    public function setNumberNewsletter($numberNewsletter)
    {
        $this->numberNewsletter = $numberNewsletter;
    }

    /**
     * @return int
     */
    public function getNumberNewsletter()
    {
        return $this->numberNewsletter;
    }

    /**
     * @param $outputName
     */
    public function setOutputName($outputName)
    {
        $this->outputName = $outputName;
    }

    /**
     * @param int $maxVariants
     */
    public function setMaxVariants($maxVariants)
    {
        $this->maxVariants = $maxVariants;
    }

    /**
     * @param int $minVariants
     */
    public function setMinVariants($minVariants)
    {
        $this->minVariants = $minVariants;
    }

    /**
     * @return int
     */
    public function getMaxVariants()
    {
        return $this->maxVariants;
    }

    /**
     * @return int
     */
    public function getMinVariants()
    {
        return $this->minVariants;
    }

    /**
     * @param string $thumbnailSizes
     */
    public function setThumbnailSizes($thumbnailSizes)
    {
        if (empty($thumbnailSizes)) {
            $this->thumbnailSizes = null;

            return;
        }
        if (is_string($thumbnailSizes)) {
            $thumbnailSizes = explode(',', $thumbnailSizes);
        }
        $this->thumbnailSizes = $thumbnailSizes;
    }

    public function getThumbnailSizes()
    {
        return $this->thumbnailSizes;
    }

    /**
     * @return int
     */
    public function getNumberVouchers()
    {
        return $this->numberVouchers;
    }

    /**
     * @param int $numberVouchers
     */
    public function setNumberVouchers($numberVouchers)
    {
        $this->numberVouchers = $numberVouchers;
    }

    /**
     * @return int
     */
    public function getArticleFilterGroups()
    {
        return $this->articleFilterGroups;
    }

    /**
     * @param int $articleFilterGroups
     */
    public function setArticleFilterGroups($articleFilterGroups)
    {
        $this->articleFilterGroups = $articleFilterGroups;
    }

    /**
     * @return int
     */
    public function getArticleFilterOptions()
    {
        return $this->articleFilterOptions;
    }

    /**
     * @param int $articleFilterOptions
     */
    public function setArticleFilterOptions($articleFilterOptions)
    {
        $this->articleFilterOptions = $articleFilterOptions;
    }

    /**
     * @return int
     */
    public function getArticleFilterValues()
    {
        return $this->articleFilterValues;
    }

    /**
     * @param int $articleFilterValues
     */
    public function setArticleFilterValues($articleFilterValues)
    {
        $this->articleFilterValues = $articleFilterValues;
    }

    /**
     * @param $imageDir
     */
    public function setImageDir($imageDir)
    {
        $dir = $imageDir . '/' . 'thumbnail';
        if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        $this->imageDir = $imageDir;
    }

    /**
     * @return string
     */
    public function getImageDir()
    {
        return $this->imageDir;
    }

    /**
     * @param  $createImages
     */
    public function setCreateImages($createImages)
    {
        $this->createImages = (bool) $createImages;
    }

    /**
     * @return bool
     */
    public function getCreateImages()
    {
        return $this->createImages;
    }

    public function createOutputDir()
    {
        if (!mkdir('output') && !is_dir('output')) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', 'output'));
        }
    }

    /**
     * @param int $chunkSize
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return int
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * @param int $seed
     */
    public function setSeed($seed)
    {
        $this->seed = $seed;
    }

    /**
     * @return string
     */
    public function getOutputName()
    {
        return $this->outputName;
    }

    /**
     * @return mixed
     */
    public function getSeed()
    {
        return $this->seed;
    }
}
