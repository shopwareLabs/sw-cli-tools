<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.store.Main', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: '<?= $configuration->name; ?>'
        };
    },
    model: 'Shopware.apps.<?= $configuration->name; ?>.model.Main'
});