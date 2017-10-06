<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

<?php if ($configuration->backendModel) { ?>
/**
 * Backend controllers extending from Shopware_Controllers_Backend_Application do support the new backend components
 */
class Shopware_Controllers_Backend_<?= $configuration->name; ?> extends Shopware_Controllers_Backend_Application
{
    protected $model = '\<?= $configuration->name; ?>\Models\<?= $configuration->backendModel; ?>';
    protected $alias = '<?= $names->under_score_model; ?>';
<?php } else { ?>
class Shopware_Controllers_Backend_<?= $configuration->name; ?> extends Shopware_Controllers_Backend_ExtJs
{
<?php } ?>


<?php if ($configuration->hasWidget) { ?>
    /**
     * Loads data for the backend widget
     */
    public function loadBackendWidgetAction()
    {
        $data = array(
            array('id' => 1, 'name' => 'Shopware'),
            array('id' => 2, 'name' => 'Shopman'),
        );

        $this->View()->assign(array(
            'success' => true,
            'data' => $data
        ));
    }
<?php } ?>
}
