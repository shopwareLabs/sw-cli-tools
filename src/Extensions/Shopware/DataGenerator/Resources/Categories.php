<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Resources;

use Shopware\DataGenerator\Writer\WriterInterface;

class Categories extends BaseResource
{
    /**
     * The array with the categories that are going to be created.
     *
     * @var array
     */
    public $categoriesFlat = [];

    /**
     * @var array
     */
    protected $tables = ['s_categories'];

    /**
     * The number of categories which have been created
     * Used in order to not create too many categories.
     */
    protected $categoriesSum;

    protected $total;

    /**
     * {@inheritdoc}
     *
     * @param string $type
     */
    public function getUniqueId($type)
    {
        ++$this->categoriesSum;
        if (empty($this->ids[$type])) {
            $this->ids[$type] = 1;

            return 1;
        }

        ++$this->ids[$type];

        return $this->ids[$type];
    }

    /**
     * {@inheritdoc}
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

        $thisCategory = [
            'id' => 1,
            'parent' => 'NULL',
            'path' => '',
            'name' => 'Root',
            'level' => 0,
            'position' => 0,
            'left' => 1,
            'right' => $germanCategory['right'] + 1,
            'children' => [$germanCategory],
        ];
        $this->categoriesFlat[1] = $thisCategory;

        $categoryUrls = $this->writerManager->createWriter('categories', 'csv');

        $categoryURLs = [];
        $values = [];
        foreach ($this->categoriesFlat as $id => $category) {
            $this->advanceProgressBar();
            $categoryURLs[] = "/cat/index/sCategory/{$category['id']}";

            $name = isset($category['name']) ? $category['name'] : $this->generator->getRandomWord();
            $values[] = "({$category['id']}, {$category['parent']}, '{$category['path']}', '{$name}', {$id}, 1)";
        }

        $categoryValues = \sprintf(
            'INSERT INTO `s_categories` (`id`, `parent`, `path`, `description`, `position`, `active`) VALUES %s ;',
            \implode(",\n             ", $values)
        );

        $this->finishProgressBar();

        $writer->write($categoryValues);
        $categoryUrls->write($categoryURLs);
    }

    /**
     * @param int        $number
     * @param int|null   $parentCategory
     * @param int        $depth
     * @param array|null $leftNeighbour
     */
    private function buildNestedTree($number, $parentCategory = null, $depth = 0, $leftNeighbour = null): array
    {
        $categoriesOnThisLevel = ($depth === 0) ? 6 : \random_int(2, 10);
        $categoriesPerChild = \round($number / $categoriesOnThisLevel);

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
            $thisCategory = [
                'id' => 3,
                'parent' => 1,
                'path' => '',
                'name' => 'Deutsch',
                'level' => 1,
                'position' => 0,
                'left' => 2,
                'right' => 887,
                'children' => [],
            ];
        } else {
            if ($categoriesPerChild < 1) {
                $id = $this->getUniqueId('finalCats');
            } else {
                $id = $this->getUniqueId('category');
            }
            $this->categoriesFlat[$id] = null;

            $path = $this->buildPath($parentCategory['id']);
            $path = '|' . \implode('|', $path) . '|';

            $this->categoriesFlat[$id] = [
                'id' => $id,
                'parent' => $parentCategory['id'],
                'path' => $path,
                'level' => ($depth + 1),
                'left' => $left,
                'right' => ($left + 1),
                'position' => 0,
                'children' => [],
            ];
            $thisCategory = $this->categoriesFlat[$id];
        }

        if ($categoriesPerChild < 1) {
            $this->categoriesFlat[$id] = $thisCategory;

            return $thisCategory;
        }

        $lastChild = null;
        for ($c = 0; $c < $categoriesOnThisLevel; ++$c) {
            $lastChild = $this->buildNestedTree($categoriesPerChild, $thisCategory, $depth + 1, $lastChild);
            $lastChild['position'] = $c;
            // we do not need the whole category tree
            // $thisCategory['children'][] = $lastChild;
        }

        $thisCategory['right'] = $lastChild['right'] + 1;

        $this->categoriesFlat[$id] = $thisCategory;

        return $thisCategory;
    }

    /**
     * Builds path for the given category.
     */
    private function buildPath($id): array
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
            $path = \array_merge($path, $this->buildPath($parent['parent']));
        }

        return $path;
    }
}
