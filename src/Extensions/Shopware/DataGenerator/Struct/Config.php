<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Struct;

class Config
{
    /**
     * Default values.
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

    protected $generatorLocale;

    /**
     * Path of the image directory.
     *
     * @var string
     */
    protected $imageDir;

    protected $createImages;

    /**
     * @var array
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

    public function setNumberArticles($numberArticles): void
    {
        $this->numberArticles = $numberArticles;
    }

    public function getNumberArticles(): int
    {
        return $this->numberArticles;
    }

    /**
     * @param int $numberCategories
     */
    public function setNumberCategories($numberCategories): void
    {
        $this->numberCategories = $numberCategories;
    }

    /**
     * @param int $categoriesPerArticle
     */
    public function setNumberCategoriesPerArticle($categoriesPerArticle): void
    {
        $this->categoriesPerArticle = \max(1, $categoriesPerArticle);
    }

    public function getCategoriesPerArticle(): int
    {
        return $this->categoriesPerArticle;
    }

    public function getNumberCategories(): int
    {
        return $this->numberCategories;
    }

    public function setNumberCustomers($numberCustomers): void
    {
        $this->numberCustomers = $numberCustomers;
    }

    public function getNumberCustomers(): int
    {
        return $this->numberCustomers;
    }

    public function setNumberOrders($numberOrders): void
    {
        $this->numberOrders = $numberOrders;
    }

    public function getNumberOrders(): int
    {
        return $this->numberOrders;
    }

    public function setNumberNewsletter($numberNewsletter): void
    {
        $this->numberNewsletter = $numberNewsletter;
    }

    public function getNumberNewsletter(): int
    {
        return $this->numberNewsletter;
    }

    public function setOutputName($outputName): void
    {
        $this->outputName = $outputName;
    }

    /**
     * @param int $maxVariants
     */
    public function setMaxVariants($maxVariants): void
    {
        $this->maxVariants = $maxVariants;
    }

    /**
     * @param int $minVariants
     */
    public function setMinVariants($minVariants): void
    {
        $this->minVariants = $minVariants;
    }

    public function getMaxVariants(): int
    {
        return $this->maxVariants;
    }

    public function getMinVariants(): int
    {
        return $this->minVariants;
    }

    /**
     * @param string|array $thumbnailSizes
     */
    public function setThumbnailSizes($thumbnailSizes): void
    {
        if (empty($thumbnailSizes)) {
            $this->thumbnailSizes = null;

            return;
        }
        if (\is_string($thumbnailSizes)) {
            $thumbnailSizes = \explode(',', $thumbnailSizes);
        }
        $this->thumbnailSizes = $thumbnailSizes;
    }

    public function getThumbnailSizes(): array
    {
        return $this->thumbnailSizes;
    }

    public function getNumberVouchers(): int
    {
        return $this->numberVouchers;
    }

    /**
     * @param int $numberVouchers
     */
    public function setNumberVouchers($numberVouchers): void
    {
        $this->numberVouchers = $numberVouchers;
    }

    public function getArticleFilterGroups(): int
    {
        return $this->articleFilterGroups;
    }

    /**
     * @param int $articleFilterGroups
     */
    public function setArticleFilterGroups($articleFilterGroups): void
    {
        $this->articleFilterGroups = $articleFilterGroups;
    }

    public function getArticleFilterOptions(): int
    {
        return $this->articleFilterOptions;
    }

    /**
     * @param int $articleFilterOptions
     */
    public function setArticleFilterOptions($articleFilterOptions): void
    {
        $this->articleFilterOptions = $articleFilterOptions;
    }

    public function getArticleFilterValues(): int
    {
        return $this->articleFilterValues;
    }

    /**
     * @param int $articleFilterValues
     */
    public function setArticleFilterValues($articleFilterValues): void
    {
        $this->articleFilterValues = $articleFilterValues;
    }

    public function setImageDir($imageDir): void
    {
        $dir = $imageDir . '/thumbnail';
        if (!\is_dir($dir) && !\mkdir($dir, 0777, true) && !\is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }
        $this->imageDir = $imageDir;
    }

    public function getImageDir(): string
    {
        return $this->imageDir;
    }

    public function setCreateImages($createImages): void
    {
        $this->createImages = (bool) $createImages;
    }

    public function getCreateImages(): bool
    {
        return $this->createImages;
    }

    public function createOutputDir(): void
    {
        $outputDir = \getcwd() . '/output';
        if (!\is_dir($outputDir) && !\mkdir($outputDir) && !\is_dir($outputDir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', 'output'));
        }
    }

    /**
     * @param int $chunkSize
     */
    public function setChunkSize($chunkSize): void
    {
        $this->chunkSize = $chunkSize;
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @param int $seed
     */
    public function setSeed($seed): void
    {
        $this->seed = $seed;
    }

    public function getOutputName(): string
    {
        return $this->outputName;
    }

    public function getSeed(): int
    {
        return $this->seed;
    }

    /**
     * @param string $generatorLocale
     */
    public function setGeneratorLocale($generatorLocale): void
    {
        $this->generatorLocale = $generatorLocale;
    }

    public function getGeneratorLocale(): string
    {
        return $this->generatorLocale;
    }
}
