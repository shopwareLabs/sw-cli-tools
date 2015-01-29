<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace Shopware\<?= $configuration->name; ?>\Subscriber;

class Frontend implements \Enlight\Event\SubscriberInterface
{
    /**
     * @var \Shopware_Plugins_<?= $configuration->namespace; ?>_<?= $configuration->name; ?>_Bootstrap
     */
    protected $bootstrap;

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontendPostDispatch'
        );
    }

    public function onFrontendPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();



    }
}