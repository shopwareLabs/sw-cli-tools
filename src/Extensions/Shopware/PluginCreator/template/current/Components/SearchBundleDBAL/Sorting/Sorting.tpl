<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Components\SearchBundleDBAL\Sorting;

use Shopware\Bundle\SearchBundle\SortingInterface;

class Sorting implements SortingInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
