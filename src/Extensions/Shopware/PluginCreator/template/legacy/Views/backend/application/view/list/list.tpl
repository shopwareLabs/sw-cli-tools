<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.view.list.List', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.<?= $names->dash_js; ?>-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.<?= $configuration->name; ?>.view.detail.Window'
        };
    }
});
