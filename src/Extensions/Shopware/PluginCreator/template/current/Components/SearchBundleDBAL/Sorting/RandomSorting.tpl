<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Components\SearchBundleDBAL\Sorting;

use Shopware\Bundle\SearchBundle\SortingInterface;

class RandomSorting implements SortingInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return '<?= $names->under_score_js; ?>_random';
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
