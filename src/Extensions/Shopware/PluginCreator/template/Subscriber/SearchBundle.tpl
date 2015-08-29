<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace Shopware\<?= $configuration->name; ?>\Subscriber;

use Shopware\<?= $configuration->name; ?>\Components\SearchBundleDBAL;


class SearchBundle implements \Enlight\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'Shopware_SearchBundleDBAL_Collect_Facet_Handlers' => 'registerFacetHandlers',
            'Shopware_SearchBundleDBAL_Collect_Condition_Handlers' => 'registerConditionHandlers',
            'Shopware_SearchBundle_Collect_Criteria_Request_Handlers' => 'registerRequestHandlers',
        );
    }

    public function registerFacetHandlers()
    {
        return new SearchBundleDBAL\Facet\<?= $configuration->name; ?>FacetHandler(
            Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory'),
            Shopware()->Container()->get('snippets')
        );
    }

    public function registerConditionHandlers()
    {
        return new SearchBundleDBAL\Condition\<?= $configuration->name; ?>ConditionHandler();
    }

    public function registerRequestHandlers()
    {
        return new SearchBundleDBAL\<?= $configuration->name; ?>CriteriaRequestHandler();
    }
}