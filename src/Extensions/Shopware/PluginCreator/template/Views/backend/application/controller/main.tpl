<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;
        me.mainWindow = me.getView('list.Window').create({ }).show();
    }
});