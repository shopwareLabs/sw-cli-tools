<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Components\SearchBundle;

use <?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition\<?= $configuration->name; ?>Condition;
use <?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet\<?= $configuration->name; ?>Facet;
use <?= $configuration->name; ?>\Components\SearchBundleDBAL\Sorting\Sorting;
use Enlight_Controller_Request_RequestHttp as Request;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class <?= $configuration->name; ?>CriteriaRequestHandler implements CriteriaRequestHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handleRequest(
        Request $request,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        if ($request->has('<?= $names->under_score_js; ?>_condition')) {
            $criteria->addCondition(new <?= $configuration->name; ?>Condition());
        }

        if ($request->get('sSort') == '<?= $names->under_score_js; ?>_random') {
            $criteria->addSorting(new Sorting());
        }

        $criteria->addFacet(new <?= $configuration->name; ?>Facet());
    }
}
