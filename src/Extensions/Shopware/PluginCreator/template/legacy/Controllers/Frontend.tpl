<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

/**
 * Frontend controller
 */
class Shopware_Controllers_Frontend_<?= $configuration->name; ?> extends Enlight_Controller_Action
{
    public function indexAction()
    {
        // Assign a template variable
        $this->View()->assign('name', '<?= $configuration->name; ?>');
    }
}
