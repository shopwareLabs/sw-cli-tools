<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace  <?= $configuration->name; ?>\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiSubscriber implements SubscriberInterface
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
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Front_DispatchLoopStartup' => 'onStartDispatch'
        ];
    }

    /**
     * This callback function is triggered at the very beginning of the dispatch process and allows
     * us to register additional events on the fly. This way you won't ever need to reinstall you
     * plugin for new events - any event and hook can simply be registered in the event subscribers
     */
    public function onStartDispatch(\Enlight_Event_EventArgs $args)
    {
        $this->container->get('Loader')->registerNamespace(
            'Shopware\Components\Api',
            $this->container->getParameter('<?= $names->under_score_js ?>.plugin_dir') . 'Components/Api/'
        );
    }
}
