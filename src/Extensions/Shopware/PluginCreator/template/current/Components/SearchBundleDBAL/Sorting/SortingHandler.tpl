<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Components\SearchBundleDBAL\Sorting;

use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\SortingHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class SortingHandler implements SortingHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsSorting(SortingInterface $sorting)
    {
    }

    /**
     * @inheritdoc
     */
    public function generateSorting(
        SortingInterface $sorting,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
    }
}