<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Subscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use <?= $configuration->name; ?>\Components\SearchBundleDBAL;
use Enlight\Event\SubscriberInterface;

class SearchBundle implements SubscriberInterface
{
    /**
    * @var ContainerInterface
    */
    private $container;

    /**
    * @param ContainerInterface $container
    */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'Shopware_SearchBundleDBAL_Collect_Facet_Handlers' => 'registerFacetHandlers',
            'Shopware_SearchBundleDBAL_Collect_Condition_Handlers' => 'registerConditionHandlers',
            'Shopware_SearchBundle_Collect_Criteria_Request_Handlers' => 'registerRequestHandlers',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing' => 'extendListingTemplate'
        );
    }

    public function registerFacetHandlers()
    {
        return new \<?= $configuration->name; ?>\Components\SearchBundleDBAL\Facet\<?= $configuration->name; ?>FacetHandler(
            Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory'),
            Shopware()->Container()->get('snippets')
        );
    }

    public function registerConditionHandlers()
    {
        return new \<?= $configuration->name; ?>\Components\SearchBundleDBAL\Condition\<?= $configuration->name; ?>ConditionHandler();
    }

    public function registerRequestHandlers()
    {
        return new \<?= $configuration->name; ?>\Components\SearchBundle\<?= $configuration->name; ?>CriteriaRequestHandler();
    }

    /**
     * Adds the sorting option in the frontend.
     */
    public function onFrontendPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
    }


    /**
    * Adds the sorting option in the frontend.
    */
    public function extendListingTemplate(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        $view->addTemplateDir(
            $this->container->getParameter('<?= $names->under_score_js ?>.plugin_dir') . '/Resources/views/'
        );
    }
}
