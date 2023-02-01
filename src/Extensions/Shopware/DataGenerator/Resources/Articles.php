<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\Services\LoadDataInfile;
use Shopware\DataGenerator\Writer\WriterInterface;

class Articles extends BaseResource
{
    private const GROUPS = 500;
    private const OPTIONS = 10;

    /**
     * @var array
     */
    protected $tables = [
        's_media',
        's_articles',
        's_articles_img',
        's_articles_prices',
        's_articles_details',
        's_articles_attributes',
        's_articles_categories',
        's_articles_categories_ro',
        's_article_configurator_sets',
        's_article_configurator_options',
        's_article_configurator_groups',
        's_article_configurator_option_relations',
        's_article_configurator_set_group_relations',
        's_article_configurator_set_option_relations',
        's_filter',
        's_filter_articles',
        's_filter_attributes',
        's_filter_options',
        's_filter_values',
        's_filter_relations',
    ];

    /**
     * @var LoadDataInfile
     */
    protected $loadDataInfile;

    /**
     * @var Categories
     */
    protected $categoryResource;

    /**
     * @var \SplFixedArray
     */
    protected $articleDetailsFlat;

    protected $configuratorsGroups = [];

    public function getArticleDetailsFlat(): \SplFixedArray
    {
        return $this->articleDetailsFlat;
    }

    public function createConfiguratorOptions(): void
    {
        for ($group = 0; $group <= self::GROUPS; ++$group) {
            $currentGroup = [
                'id' => $this->getUniqueId('conf_group'),
                'name' => 'Configurator Group ' . $group,
                'position' => $group,
                'options' => [],
            ];
            for ($option = 0; $option <= self::OPTIONS; ++$option) {
                $currentGroup['options'][] = [
                    'id' => $this->getUniqueId('conf_option'),
                    'name' => 'Option' . $option,
                    'position' => $option,
                ];
            }
            $this->configuratorsGroups[] = $currentGroup;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(WriterInterface $writer)
    {
        $this->createConfiguratorOptions();

        $number = $this->config->getNumberArticles();
        $this->loadDataInfile = new LoadDataInfile();

        $urls = $this->writerManager->createWriter('article_urls', 'csv');

        $articles = $this->writerManager->createWriter('articles', 'csv');
        $details = $this->writerManager->createWriter('articles_details', 'csv');
        $prices = $this->writerManager->createWriter('articles_price', 'csv');
        $attributes = $this->writerManager->createWriter('articles_attributes', 'csv');
        $configuratorSets = $this->writerManager->createWriter('articles_configurator_set', 'csv');
        $configuratorGroups = $this->writerManager->createWriter('articles_configurator_group_values', 'csv');
        $configuratorOptions = $this->writerManager->createWriter('articles_configurator_option_values', 'csv');
        $configuratorSetOptionRelations = $this->writerManager->createWriter(
            'articles_configurator_set_option_relation',
            'csv'
        );
        $configuratorSetGroupRelations = $this->writerManager->createWriter(
            'articles_configurator_set_group_relation',
            'csv'
        );
        $articleCategories = $this->writerManager->createWriter('articles_categories', 'csv');
        $articlesCategoriesRO = $this->writerManager->createWriter('s_articles_categories_ro', 'csv');
        $filterArticles = $this->writerManager->createWriter('filter_articles', 'csv');
        $articleConfiguratorRelations = $this->writerManager->createWriter(
            'article_configurator_option_relations',
            'csv'
        );

        if ($this->config->getNumberOrders() > 0) {
            $maxDetails = $this->config->getNumberArticles() * $this->config->getMaxVariants();
            $this->articleDetailsFlat = new \SplFixedArray($maxDetails);
        }

        if (!$this->categoryResource->categoriesFlat) {
            throw new \RuntimeException('Category resource not found');
        }

        $categoriesFlat = $this->categoryResource->categoriesFlat;
        $validCategoryIds = \array_filter(
            \array_keys($categoriesFlat),
            static function ($item) {
                return $item >= 1000000;
            }
        );
        $categoriesPerArticle = \min(\count($validCategoryIds), $this->config->getCategoriesPerArticle());
        if (\count($validCategoryIds) < $this->config->getCategoriesPerArticle()) {
            $this->ioService->writeln(
                '<comment>Number of categories per article will be lower than specified</comment>'
            );
        }

        $this->createProgressBar($number);

        $images = [];

        $priceVariations = $this->generatePriceVariations($number);

        // Write all configurators and options beforehand

        foreach ($this->configuratorsGroups as $currentGroup) {
            $configuratorGroups->write(
                "{$currentGroup['id']}, {$currentGroup['name']},NULL, {$currentGroup['position']}"
            );

            foreach ($currentGroup['options'] as $currentOption) {
                $configuratorOptions->write(
                    "{$currentOption['id']}, {$currentGroup['id']}, {$currentOption['name']}, {$currentOption['position']}"
                );
            }
        }

        for ($articleCounter = 0; $articleCounter < $number; ++$articleCounter) {
            $this->advanceProgressBar();
            $id = $this->getUniqueId('article');
            $createConfigurator = $id === 1 ? 1 : \random_int(0, 1); // Force the first article to be a configurator
            // number of variants is chosen randomly
            $numberOfVariants = $createConfigurator === 1 ? \random_int(
                $this->config->getMinVariants(),
                $this->config->getMaxVariants()
            ) : 1;

            if ($numberOfVariants === 1) {
                $createConfigurator = 0;
            }

            $createFilter = \random_int(0, 3);

            $urls->write("/detail/index/sArticle/{$id}");

            // Get the id of the first articleDetail in advance on order to set the main_detail_id properly
            $articleDetailId = $this->getUniqueId('articleDetail');
            $detailIDs = [$articleDetailId];

            $configuratorSetId = $createConfigurator === 1 ? $id : 'NULL';

            // number of variants determines number of groups, as number of options is static
            $numberOfGroups = \max(1, $numberOfVariants / self::OPTIONS);

            // number of variants will be a multiple of self::OPTIONS so it is re-calculated here
            if ($createConfigurator === 1) {
                $numberOfVariants = self::OPTIONS * $numberOfGroups;
            }

            //
            // Configurator
            //
            if ($createConfigurator) {
                $groups = [];
                $options = [];
                $configuratorSets->write("{$id}, Test-Configurator-Set Article {$id}");
                $randomGroups = \array_rand($this->configuratorsGroups, $numberOfGroups);
                // array_rand will not return an array if only one element was chosen
                if (!\is_array($randomGroups)) {
                    $randomGroups = [$randomGroups];
                }

                // Create configurator groups and options
                foreach ($randomGroups as $g) {
                    $currentGroup = $this->configuratorsGroups[$g];
                    // Create options for this group
                    $curOptions = \array_column($this->configuratorsGroups[$g]['options'], 'id');
                    $options = \array_merge($curOptions, $options);
                    $groups[$currentGroup['id']] = $curOptions;
                }

                /*
                 * create a cartesian product of the available options in order to simply assign them to articles later
                 */
                $allOptions = $this->createCartesianProduct($groups);

                // Create relations
                // set-group-relations
                foreach (\array_keys($groups) as $groupId) {
                    $configuratorSetGroupRelations->write("{$id}, {$groupId}");
                }

                // set-option-relations
                foreach ($options as $optionId) {
                    $configuratorSetOptionRelations->write("{$id}, {$optionId}");
                }
            }

            \shuffle($validCategoryIds);
            for ($i = 0; $i < $categoriesPerArticle; ++$i) {
                $articleCategories->write("{$id}, {$validCategoryIds[$i]}");

                $categoryIds = $this->getCategoryPath($validCategoryIds[$i], $categoriesFlat);
                if (empty($categoryIds)) {
                    continue;
                }

                $last = $categoryIds[0];
                foreach ($categoryIds as $categoryId) {
                    $articlesCategoriesRO->write("{$id}, {$categoryId}, {$last}");
                }
            }

            if ($this->config->getCreateImages()) {
                throw new \Exception('Not implemented, yet');
                // Images
                for ($i = 1; $i <= $numImagesPerArticle; ++$i) {
                    $mediaId = $this->getUniqueId('media');
                    $images[] = $physicallyCreateEachImage ? $baseName . $id : $baseName;
                    $mediaValues[] = "({$mediaId}, -1, {$name}, media/image/{$name}.jpg, IMAGE, jpg, 2012-08-15 )";
                    $main = ($i === 1) ? 1 : 2;
                    $articlesImgValues[] = "({$id},{$name}, {$main}, jpg, {$mediaId} )";
                }
            }

            $filterOptions = $this->config->getArticleFilterOptions();
            $filterValues = $this->config->getArticleFilterValues();

            // Filters
            $filterGroupId = 1;
            if ($createFilter === 0 && $filterOptions > 0) {
                $filterGroupId = \random_int(1, $filterOptions);
                for ($option = 1; $option <= $filterOptions; ++$option) {
                    $optionId = $option * $filterGroupId;
                    $valueId = \random_int(1, $filterValues) * $optionId;
                    $filterArticles->write("{$id}, {$valueId}");
                }
            }

            //
            // Article / -details
            //
            $name = \rtrim($this->generator->getSentence(3), '.');
            $articles->write(
                "{$id}, 2, {$name}, SHORT DESCRIPTION, LONG DESCRIPTION, NULL, 2012-08-15, 1, 1, 20, 0, , 2012-08-30 16:57:00, 1, 0, {$filterGroupId}, 0, 0, 0, , 0, {$articleDetailId}, NULL, NULL, {$configuratorSetId}"
            );

            // Create article details / variants
            for ($i = 1; $i <= $numberOfVariants; ++$i) {
                if ($this->config->getNumberOrders() > 0) {
                    $this->articleDetailsFlat[$articleDetailId] = "{$id}|sw-{$id}-{$i}|{$articleDetailId}";
                }
                $kind = $i === 1 ? 1 : 2;

                $purchaseUnit = \random_int(0, 5);
                $referenceUnit = \random_int($purchaseUnit, $purchaseUnit * 4);

                $details->write(
                    "{$articleDetailId}, {$id},sw-{$id}-{$i}, , {$kind}, , 0, 1, 25, 0, 0.000, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1, {$purchaseUnit}, {$referenceUnit}, Flasche(n), 2012-06-13, 0, "
                );

                $index = \random_int(0, \count($priceVariations) - 1);
                foreach ($priceVariations[$index] as $price) {
                    $prices->write(
                        "EK,{$price['from']},{$price['to']},{$id},{$articleDetailId},{$price['price']}, 0, 0, 0 "
                    );
                    $attributes->write("{$id}, {$articleDetailId}");
                }

                // don't get a new id after the last detail
                if ($i < $numberOfVariants) {
                    $articleDetailId = $this->getUniqueId('articleDetail');
                    $detailIDs[] = $articleDetailId;
                }
            }

            // Set articleDetail-option relation
            if ($createConfigurator) {
                foreach ($detailIDs as $detailID) {
                    $options = \array_pop($allOptions);
                    if (empty($options)) {
                        continue;
                    }
                    foreach ($options as $option) {
                        $articleConfiguratorRelations->write("{$detailID}, " . $option);
                    }
                }
            }
        }

        if ($this->config->getCreateImages()) {
            $this->copyImages(
                $this->config->getImageDir(),
                $images,
                $this->config->getThumbnailSizes(),
                1
            );
        }

        $writer->write($this->loadDataInfile->get('s_articles', $articles->getFileName()));
        $writer->write($this->loadDataInfile->get('s_articles_details', $details->getFileName()));
        $writer->write($this->loadDataInfile->get('s_articles_prices', $prices->getFileName()));

        $writer->write($this->loadDataInfile->get('s_articles_categories', $articleCategories->getFileName()));
        $writer->write(
            $this->loadDataInfile->get('s_articles_categories_ro', $articlesCategoriesRO->getFileName())
        );
        $writer->write($this->loadDataInfile->get('s_articles_attributes', $attributes->getFileName()));
        $writer->write(
            $this->loadDataInfile->get('s_article_configurator_sets', $configuratorSets->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get(
                's_article_configurator_option_relations',
                $articleConfiguratorRelations->getFileName()
            )
        );
        $writer->write(
            $this->loadDataInfile->get('s_article_configurator_groups', $configuratorGroups->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get('s_article_configurator_options', $configuratorOptions->getFileName())
        );
        $writer->write(
            $this->loadDataInfile->get(
                's_article_configurator_set_option_relations',
                $configuratorSetOptionRelations->getFileName()
            )
        );
        $writer->write(
            $this->loadDataInfile->get(
                's_article_configurator_set_group_relations',
                $configuratorSetGroupRelations->getFileName()
            )
        );

        $writer->write($this->loadDataInfile->get('s_filter_articles', $filterArticles->getFileName()));

        $this->createFilterGroupSQL($writer);

        $this->finishProgressBar();
    }

    public function getCategoryResource(): Categories
    {
        return $this->categoryResource;
    }

    public function setCategoryResource(Categories $categoryResource): void
    {
        $this->categoryResource = $categoryResource;
    }

    /**
     * Generates SQL which creates filter groups, options and values.
     */
    protected function createFilterGroupSQL(WriterInterface $importWriter): void
    {
        $filterGroupValues = [];
        $filterOptionValues = [];
        $filterValueValues = [];
        $filterOptionGroupRelationValues = [];

        $filterGroups = $this->config->getArticleFilterGroups();
        $filterOptions = $this->config->getArticleFilterOptions();
        $filterValues = $this->config->getArticleFilterValues();

        for ($groupId = 1; $groupId <= $filterGroups; ++$groupId) {
            $filterGroupValues[] = "$groupId, Filtergroup #{$groupId}, {$groupId}, " . \random_int(0, 1) . ', ' . \random_int(0, 1);

            for ($o = 1; $o <= $filterOptions; ++$o) {
                $optionId = $o + ($groupId - 1) * $filterOptions;
                $filterOptionValues[] = "$optionId, Option #{$o},  " . \random_int(0, 1);
                $filterOptionGroupRelationValues[] = "$groupId, $optionId, $o";

                for ($v = 1; $v <= $filterValues; ++$v) {
                    $valueId = $v + ($optionId - 1) * $filterValues;
                    $filterValueValues[] = "$valueId, $optionId, Value #{$valueId}, $valueId";
                }
            }
        }

        $filter = $this->writerManager->createWriter('filter', 'csv');
        $filterOptions = $this->writerManager->createWriter('filter_options', 'csv');
        $filterValues = $this->writerManager->createWriter('filter_values', 'csv');
        $filterRelations = $this->writerManager->createWriter('filter_relations', 'csv');

        $filter->write($filterGroupValues);
        $filterOptions->write($filterOptionValues);
        $filterValues->write($filterValueValues);
        $filterRelations->write($filterOptionGroupRelationValues);

        $importWriter->write($this->loadDataInfile->get('s_filter', $filter->getFileName()));
        $importWriter->write($this->loadDataInfile->get('s_filter_options', $filterOptions->getFileName()));
        $importWriter->write($this->loadDataInfile->get('s_filter_values', $filterValues->getFileName()));
        $importWriter->write($this->loadDataInfile->get('s_filter_relations', $filterRelations->getFileName()));
    }

    /**
     * Helper function which creates a cartesian product.
     */
    private function createCartesianProduct($arrays): array
    {
        $cartesian = [];
        foreach (\array_reverse($arrays) as $dimName => $dim) {
            $buf = [];

            foreach ($dim as $val) {
                $buf[] = [$dimName => $val];
            }

            if (!\count($cartesian)) {
                $cartesian = $buf;
            } else {
                $tmp = [];
                foreach ($buf as $elBuf) {
                    foreach ($cartesian as $elAp) {
                        $tmp[] = \array_merge($elBuf, $elAp);
                    }
                }
                $cartesian = $tmp;
            }
        }

        return $cartesian;
    }

    private function getCategoryPath($id, $categories): array
    {
        if (!$id) {
            return [];
        }
        if (!isset($categories[$id])) {
            return [];
        }
        if ($id == 1) {
            return [];
        }

        $category = $categories[$id];
        $result[] = $id;
        if ($category['parent']) {
            $parent = $this->getCategoryPath($category['parent'], $categories);
            $result = \array_merge($result, $parent);
        }

        return $result;
    }

    /**
     * Copies the default image for each article.
     *
     * @param int $useSmallImage
     */
    private function copyImages($imageDir, $images, $thumbs, $useSmallImage): void
    {
        $assetsDir = ''; // TODO: implement real asset loading
        // Copy the images to media directory
        $destination = $assetsDir . ($useSmallImage ? '/images/beach_small.jpg' : '/images/beach.jpg');
        foreach ($images as $imageName) {
            $target = $imageDir . '/' . $imageName . '.jpg';
            \copy($destination, $target);

            foreach ($thumbs as $size) {
                $target = $imageDir . '/thumbnail/' . $imageName . '_' . $size . '.jpg';
                \copy($destination, $target);
            }
        }
    }

    /**
     * @param int $number
     */
    private function generatePriceVariations($number): array
    {
        $count = $number / 5;

        $variations = [];

        for ($v = 0; $v <= $count; ++$v) {
            // create random number of bulk prices between 1 and 5
            $priceCount = \random_int(1, 5);
            $to = 0;

            // price will be something between 3 and 2000
            $price = \random_int(3, 2000);
            for ($i = 1; $i <= $priceCount; ++$i) {
                $from = $to + 1;
                $to = $from + \random_int(2, 4);
                if ($i == $priceCount) {
                    $to = 'beliebig';
                }
                $price = $price * (100 - \random_int(10, 40)) / 100;

                $variations[$v][] = [
                    'from' => $from,
                    'to' => $to,
                    'price' => $price,
                ];
            }
        }

        return $variations;
    }
}
