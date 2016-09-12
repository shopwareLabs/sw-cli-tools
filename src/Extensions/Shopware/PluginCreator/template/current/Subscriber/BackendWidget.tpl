<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackendWidget implements SubscriberInterface
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

    /**
    * @return array
    */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatch_Backend_Index' => 'extendsBackendWidget',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_<?= $names->backendWidgetController; ?>' => 'getBackendWidgetController'
        );
    }

    public function extendsBackendWidget(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        if ($controller->Request()->getActionName() !== 'index') {
            return;
        }

        $dir = $this->container->getParameter('<?= $names->under_score_js ?>.plugin_dir');
        $controller->View()->addTemplateDir($dir . '/Resources/views/');
        $controller->View()->extendsTemplate('backend/<?= $names->under_score_js; ?>/widgets/<?= $names->under_score_js; ?>.js');
    }

    /**
    * Register the backend widget controller
    *
    * @param   \Enlight_Event_EventArgs $args
    * @return  string
    */
    public function getBackendWidgetController(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Backend/<?= $names->backendWidgetController; ?>.php';
    }
}
