<?= $configuration->licenseHeader; ?>

Ext.define('Shopware.apps.<?= $configuration->name; ?>.view.detail.Window', {
    extend: 'Shopware.window.Detail',
    alias: 'widget.<?= $names->dash_js; ?>-detail-window',

    title : '{s name=title}<?= $configuration->name; ?> details{/s}',
    height: 420,
    width: 900
});
