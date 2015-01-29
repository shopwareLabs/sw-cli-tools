<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.model.Main', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: '<?= $configuration->name; ?>',
            detail: 'Shopware.apps.<?= $configuration->name; ?>.view.detail.Container'
        };
    },


    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'name', type: 'string', useNull: false }
    ]
});

