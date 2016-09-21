<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.view.detail.Container', {
    extend: 'Shopware.model.Container',
    padding: 20,

    configure: function() {
        return {
            controller: '<?= $configuration->name; ?>'
        };
    }
});