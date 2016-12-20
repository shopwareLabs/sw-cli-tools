<?php

namespace Shopware\DataGenerator\Struct;

class Config
{
    /**
     * Default values
     * @var
     */
    protected $numberArticles = 0;
    protected $numberCategories = 0;
    protected $numberCustomers = 0;
    protected $numberOrders = 0;
    protected $numberNewsletter = 0;
    protected $numberVouchers = 0;
    protected $categoriesPerArticle = 3;
    protected $articleFilterGroups = 1;
    protected $articleFilterOptions = 15;
    protected $articleFilterValues = 20;

    /**
     * Path of the image directory
     * @var
     */
    protected $imageDir;

    /**
     * @var
     */
    protected $createImages;

    /**
     * @var
     */
    protected $thumbnailSizes;

    /**
     * Number of variants each configurator article will have at least
     * @var int
     */
    protected $minVariants;

    /**
     * Number of variants each configurator article will have at most
     * @var
     */
    protected $maxVariants;

    /**
     * Output file for the sql file
     *
     * @var string
     */
    protected $outputName;

    /**
     * Seed used to init the random generator
     * Using a default seed here, so usually you will get reproducible random numbers
     * @var int
     */
    protected $seed;

    /**
     * Number of values per insert
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

    public function getNumberArticles()
    {
        return $this->numberArticles;
    }

    public function setNumberCategories($numberCategories)
    {
        $this->numberCategories = $numberCategories;
    }

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

    public function getNumberCategories()
    {
        return $this->numberCategories;
    }

    public function setNumberCustomers($numberCustomers)
    {
        $this->numberCustomers = $numberCustomers;
    }

    public function getNumberCustomers()
    {
        return $this->numberCustomers;
    }

    public function setNumberOrders($numberOrders)
    {
        $this->numberOrders = $numberOrders;
    }

    public function getNumberOrders()
    {
        return $this->numberOrders;
    }

    public function setNumberNewsletter($numberNewsletter)
    {
        $this->numberNewsletter = $numberNewsletter;
    }

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


    public function setMaxVariants($maxVariants)
    {
        $this->maxVariants = $maxVariants;
    }

    public function setMinVariants($minVariants)
    {
        $this->minVariants = $minVariants;
    }

    /**
     * @return
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
        $dir = $imageDir.'/'.'thumbnail';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->imageDir = $imageDir;
    }

    /**
     * @return
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
        $this->createImages = (bool)$createImages;
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
        if (!is_dir('output')) {
            mkdir('output');
        }
    }

    /**
     * @param  $chunkSize
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
