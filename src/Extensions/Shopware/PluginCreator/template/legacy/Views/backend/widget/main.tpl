<?= $configuration->licenseHeader; ?>

//{block name="backend/index/application" append}
Ext.define('Shopware.apps.<?= $configuration->name; ?>.widgets.<?= $configuration->name; ?>', {
    extend: 'Shopware.apps.Index.view.widgets.Base',

    alias: 'widget.<?= $names->under_score_js; ?>',

    layout: 'fit',

    initComponent: function () {
        var me = this;

        me.items = me.getItems();

        me.callParent(arguments);
    },

    getItems: function () {
        var me = this;

        return [
            {
                xtype: 'grid',
                store: me.getWidgetStore(),
                viewConfig: {
                    hideLoadingMsg: true
                },
                border: 0,
                columns: [
                    {
                        dataIndex: 'id',
                        header: 'ID',
                        flex: 1
                    },
                    {
                        dataIndex: 'name',
                        header: 'Name',
                        flex: 1
                    }
                ]
            }
        ];
    },

    getWidgetStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields: [
                { name: 'id', type: 'integer' },
                { name: 'name', type: 'string' }
            ],
            proxy: {
                type: 'ajax',
                url: '{url controller=<?= $names->backendWidgetController; ?> action=loadBackendWidget}',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            autoLoad: true
        });
    }



});
//{/block}