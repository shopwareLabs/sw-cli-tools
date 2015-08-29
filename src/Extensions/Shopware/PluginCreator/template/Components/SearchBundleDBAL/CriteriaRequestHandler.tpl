<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace Shopware\<?= $configuration->name; ?>\Components\SearchBundleDBAL;

use \Enlight_Controller_Request_Request;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class <?= $configuration->name; ?>CriteriaRequestHandler implements CriteriaRequestHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handleRequest(
        Enlight_Controller_Request_Request $request,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        if ($request->has('<?= $names->under_score_js; ?>_condition')) {
            $criteria->addCondition(new Condition\<?= $configuration->name; ?>Condition());
        }

        $criteria->addFacet(new Facet\<?= $configuration->name; ?>Facet());
    }
}