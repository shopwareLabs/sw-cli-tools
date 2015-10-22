<?php

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\Writer\WriterInterface;

class Categories extends BaseResource
{
    /**
     * @var array
     */
    protected $tables = ['s_categories'];

    /**
     * The array with the categories that are going to be created
     * @var
     */
    public $categoriesFlat = array();

    /**
     * The number of categories which have been created
     * Used in order to not create too many categories
     * @var
     */
    protected $categoriesSum;

    /**
     * @var
     */
    protected $total;

    /**
     * @param int $number
     * @param int|null $parentCategory
     * @param int $depth
     * @param array|null $leftNeighbour
     * @return array
     */
    private function buildNestedTree($number, $parentCategory = null, $depth = 0, $leftNeighbour = null)
    {
        $categoriesOnThisLevel = ($depth === 1) ? 6 : rand(2, 10);
        $categoriesPerChild = round($number / $categoriesOnThisLevel);

        // Due to round and random calculation of subcategories
        // maximum number of categories is exceeded in most cases
        // Do no create any more subcategories if this is the case
        if ($this->categoriesSum > $this->total) {
            $categoriesPerChild = 0;
            $categoriesOnThisLevel = 1;
        }

        if ($leftNeighbour) {
            $left = $leftNeighbour['right'] + 1;
        } elseif ($parentCategory) {
            $left = $parentCategory['left'] + 1;
        }

        // Create the german language category on the first call
        if (!$parentCategory) {
            $id = 3;
            $thisCategory = array(
                "id" => 3,
                "parent" => 1,
                'path' => '',
                "name" => 'Deutsch',
                "level" => 1,
                "position" => 0,
                "left" => 2,
                "right" => 887,
                "children" => array()
            );
        } else {
            if ($categoriesPerChild < 1) {
                $id = $this->getUniqueId("finalCats");
            } else {
                $id = $this->getUniqueId("category");
            }
            $this->categoriesFlat[$id] = null;

            $path = $this->buildPath($parentCategory['id']);
            $path = '|'.implode('|', $path).'|';

            $this->categoriesFlat[$id] = array(
                "id" => $id,
                "parent" => $parentCategory['id'],
                'path' => $path,
                "level" => ($depth + 1),
                "left" => $left,
                "right" => ($left + 1),
                "position" => 0,
                "children" => array()
            );
            $thisCategory = $this->categoriesFlat[$id];
        }

        if ($categoriesPerChild < 1) {
            $this->categoriesFlat[$id] = $thisCategory;

            return $thisCategory;
        }

        $lastChild = null;
        for ($c = 0; $c < $categoriesOnThisLevel; $c++) {
            $lastChild = $this->buildNestedTree($categoriesPerChild, $thisCategory, ($depth + 1), $lastChild);
            $lastChild['position'] = $c;
            // we do not need the whole category tree
            // $thisCategory['children'][] = $lastChild;
        }

        $thisCategory['right'] = $lastChild['right'] + 1;

        $this->categoriesFlat[$id] = $thisCategory;

        return $thisCategory;
    }

    /**
     * Builds path for the given category
     *
     * @param $id
     * @return array
     */
    private function buildPath($id)
    {
        if (!$id) {
            return [];
        }
        $path = [$id];

        if (!isset($this->categoriesFlat[$id])) {
            return $path;
        }

        $parent = $this->categoriesFlat[$id];
        if (isset($parent['parent'])) {
            $path = array_merge($path, $this->buildPath($parent['parent']));
        }

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getUniqueId($type)
    {
        $this->categoriesSum++;
        if (empty($this->ids[$type])) {
            $this->ids[$type] = 1;

            return 1;
        }

        $this->ids[$type] += 1;

        return $this->ids[$type];
    }

    /**
     * @inheritdoc
     */
    public function create(WriterInterface $writer)
    {
        // Start with id 4
        $this->ids['category'] = 3;
        $this->ids['finalCats'] = 1000000;

        $this->total = $this->config->getNumberCategories();

        $this->createProgressBar($this->total);

        $this->categoriesFlat[1] = null;
        $this->categoriesFlat[3] = null;
        $germanCategory = $this->buildNestedTree($this->config->getNumberCategories());

        $thisCategory = array(
            "id" => 1,
            "parent" => 'NULL',
            'path' => '',
            "name" => 'Root',
            "level" => 0,
            "position" => 0,
            "left" => 1,
            "right" => $germanCategory['right'] + 1,
            "children" => array($germanCategory)
        );
        $this->categoriesFlat[1] = $thisCategory;

        $categoryUrls = $this->writerManager->createWriter('categories', 'csv');

        $categoryURLs = array();
        $values = array();
        foreach ($this->categoriesFlat as $id => $category) {
            $this->advanceProgressBar();
            $categoryURLs[] = "/cat/index/sCategory/{$category['id']}";

            $name = isset($category['name']) ? $category['name'] : $this->generator->getRandomWord();
            $values[] = "({$category['id']}, {$category['parent']}, '{$category['path']}', '{$name}', {$id}, {$category['left']}, {$category['right']}, {$category['level']},'2012-07-30 15:24:59', '2012-07-30 15:24:59', NULL, NULL, NULL,NULL, NULL, NULL, 1, 0, 0, NULL, 0, 0, 0)";
        }

        $categoryValues = sprintf(
            "INSERT INTO `s_categories` (`id`, `parent`, `path`, `description`, `position`, `left`, `right`, `level`, `added`, `changed`, `metakeywords`, `metadescription`, `cmsheadline`, `cmstext`, `template`, `noviewselect`, `active`, `blog`, `showfiltergroups`, `external`, `hidefilter`, `hidetop`, `mediaID`) VALUES %s ;",
            implode(",\n             ", $values)
        );
        $urls = implode("\n", $categoryURLs)."\n";

        $this->finishProgressBar();

        $writer->write($categoryValues);
        $categoryUrls->write($urls);
    }
}
