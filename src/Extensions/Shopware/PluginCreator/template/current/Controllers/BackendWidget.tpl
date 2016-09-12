<?= $configuration->phpFileHeader; ?>
<?= $configuration->licenseHeader; ?>

class Shopware_Controllers_Backend_<?= $names->backendWidgetController; ?> extends Shopware_Controllers_Backend_ExtJs
{
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
}
