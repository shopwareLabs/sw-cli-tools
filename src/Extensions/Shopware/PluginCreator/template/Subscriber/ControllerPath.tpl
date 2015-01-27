<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

namespace Shopware\<?= $configuration->name; ?>\Subscriber;

class ControllerPath implements \Enlight\Event\SubscriberInterface
{
    /**
     * @var \Shopware_Plugins_<?= $configuration->namespace; ?>_<?= $configuration->name; ?>_Bootstrap
     */
    protected $bootstrap;

    public static function getSubscribedEvents()
    {
        return array(
            <?php if ($configuration->hasBackend || $configuration->hasWidget) { ?>
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_<?= $configuration->name; ?>' => 'onGetControllerPathBackend',<?php } ?><?php if ($configuration->hasFrontend) { ?>
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_<?= $configuration->name; ?>' => 'onGetControllerPathFrontend',<?php } ?><?php if ($configuration->hasWidget) { ?>
            'Enlight_Controller_Action_PostDispatch_Backend_Index' => 'extendsBackendWidget',<?php } ?><?php if ($configuration->hasApi) { ?>
            'Enlight_Controller_Dispatcher_ControllerPath_Api_<?= $names->camelCaseModel; ?>' => 'getApiController<?= $names->camelCaseModel; ?>'<?php } ?>
        );
    }

    public function __construct(\Shopware_Plugins_<?= $configuration->namespace; ?>_<?= $configuration->name; ?>_Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }
<?php if ($configuration->hasApi) { ?>

    public function getApiController<?= $names->camelCaseModel; ?>(\Enlight_Event_EventArgs $args)
    {
        $this->bootstrap->registerCustomModels();

        return $this->bootstrap->Path() . 'Controllers/Api/<?= $names->camelCaseModel; ?>.php';
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
        $this->bootstrap->registerMyTemplateDir();
        $this->bootstrap->registerMySnippets();

        return $this->bootstrap->Path() . 'Controllers/Backend/<?= $configuration->name; ?>.php';
    }
<?php } ?>

<?php if ($configuration->hasWidget) { ?>
    public function extendsBackendWidget(\Enlight_Event_EventArgs $args)
    {
        $this->bootstrap->registerMyTemplateDir();
        $this->bootstrap->registerMySnippets();

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        if ($controller->Request()->getActionName() != 'index') {
            return;
        }


        $controller->View()->extendsTemplate('backend/<?= $names->under_score_js; ?>/widgets/<?= $names->under_score_js; ?>.js');
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
        $this->bootstrap->registerMyTemplateDir();
        $this->bootstrap->registerMySnippets();

        return $this->bootstrap->Path() . 'Controllers/Frontend/<?= $configuration->name; ?>.php';
    }
<?php } ?>
}