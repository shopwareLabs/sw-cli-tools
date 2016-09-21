<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

<?php if ($configuration->isLegacyPlugin) { ?>
namespace <?= $configuration->pluginConfig['namespace']; ?>\<?= $configuration->name; ?>\Subscriber;
<?php } else { ?>
namespace <?= $configuration->name; ?>\Subscriber;
<?php } ?>

use <?= $configuration->name; ?>\Components\SearchBundleDBAL;
use Enlight\Event\SubscriberInterface;

class SearchBundle implements SubscriberInterface
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
        return new \Shopware\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet\<?= $configuration->name; ?>FacetHandler(
            Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory'),
            Shopware()->Container()->get('snippets')
        );
    }

    public function registerConditionHandlers()
    {
        return new \Shopware\<?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition\<?= $configuration->name; ?>ConditionHandler();
    }

    public function registerRequestHandlers()
    {
        return new \Shopware\<?= $configuration->name; ?>\Components\SearchBundleDBAL\<?= $configuration->name; ?>CriteriaRequestHandler();
    }
}
