<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace <?= $configuration->name; ?>\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerPath implements SubscriberInterface
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
            <?php if ($configuration->hasBackend) { ?>
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_<?= $configuration->name; ?>' => 'onGetControllerPathBackend',<?php } ?><?php if ($configuration->hasFrontend) { ?>
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_<?= $configuration->name; ?>' => 'onGetControllerPathFrontend',<?php } ?><?php if ($configuration->hasApi) { ?>
            'Enlight_Controller_Dispatcher_ControllerPath_Api_<?= $names->camelCaseModel; ?>' => 'getApiController<?= $names->camelCaseModel; ?>'<?php } ?>
        );
    }

<?php if ($configuration->hasApi) { ?>

    public function getApiController<?= $names->camelCaseModel; ?>(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Api/<?= $names->camelCaseModel; ?>.php';
    }
<?php } ?>

<?php if ($configuration->hasBackend || $configuration->hasWidget) { ?>
    /**
     * Register the backend controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_<?= $configuration->name; ?>
     */
    public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args)
    {
        $this->container->get('template')->addTemplateDir(__DIR__ . '/..' . '/Resources/views/');
        return __DIR__ . '/../Controllers/Backend/<?= $configuration->name; ?>.php';
    }
<?php } ?>

<?php if ($configuration->hasFrontend) { ?>
    /**
     * Register the frontend controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Frontend_<?= $configuration->name; ?>
     */
    public function onGetControllerPathFrontend(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Frontend/<?= $configuration->name; ?>.php';
    }
<?php } ?>
}
