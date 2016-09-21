<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.<?= $names->dash_js; ?>-list-window',
    height: 450,
    title : '{s name=window_title}<?= $configuration->name; ?> listing{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.<?= $configuration->name; ?>.view.list.List',
            listingStore: 'Shopware.apps.<?= $configuration->name; ?>.store.Main'
        };
    }
});