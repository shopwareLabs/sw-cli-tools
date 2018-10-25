<?= '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL; ?>
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
<?php if ($configuration->hasFrontend) { ?>
        <!-- @todo Add your services here -->
        <service id="<?= $names->under_score_js ?>.subscriber.frontend" class="<?= $configuration->name; ?>\Subscriber\Frontend">
            <tag name="shopware.event_subscriber" />
        </service>
<?php } ?>

<?php if ($configuration->hasBackend || $configuration->hasFrontend || $configuration->hasApi) { ?>
        <service id="<?= $names->under_score_js ?>.subscriber.controller_path" class="<?= $configuration->name; ?>\Subscriber\ControllerPath">
            <argument type="service" id="service_container" />
            <tag name="shopware.event_subscriber" />
        </service>
<?php } ?>

<?php if ($configuration->hasWidget) { ?>
        <service id="<?= $names->under_score_js ?>.subscriber.backend_widget" class="<?= $configuration->name; ?>\Subscriber\BackendWidget">
            <argument type="service" id="service_container" />
            <tag name="shopware.event_subscriber" />
        </service>
<?php } ?>

<?php if ($configuration->hasFilter) { ?>
        <service id="<?= $names->under_score_js ?>.subscriber.search_bundle" class="<?= $configuration->name; ?>\Subscriber\SearchBundle">
            <tag name="shopware.event_subscriber" />
            <argument type="service" id="service_container"></argument>
        </service>

        <service id="<?= $names->under_score_js; ?>.random_sorting.random_sorting" class="<?= $configuration->name; ?>\Components\SearchBundleDBAL\Sorting\SortingHandler">
            <tag name="sorting_handler_dbal"></tag>
        </service>
<?php } ?>


<?php if ($configuration->hasApi) { ?>
        <service id="<?= $names->under_score_js ?>.subscriber.api_subscriber" class="<?= $configuration->name; ?>\Subscriber\ApiSubscriber">
            <argument id="service_container" type="service" />
            <tag name="shopware.event_subscriber" />
        </service>
<?php } ?>

<?php if ($configuration->hasCommands) { ?>
        <service id="<?= $names->under_score_js ?>.commands.<?= $names->under_score_model ?>" class="<?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>">
            <tag name="console.command" />
        </service>
<?php } ?>

<?php if($configuration->hasElasticSearch) { ?>
        <service id="<?= $names->under_score_js ?>.search.provider" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\Provider">
            <argument id="dbal_connection" type="service" />
        </service>

        <service id="<?= $names->under_score_js ?>.data_indexer.data_indexer" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\DataIndexer">
            <tag name="shopware_elastic_search.data_indexer" />
            <argument id="dbal_connection" type="service"/>
            <argument id="shopware_elastic_search.client" type="service"/>
            <argument id="<?= $names->under_score_js ?>.search.provider" type="service"/>
        </service>

        <service id="<?= $names->under_score_js ?>.mapping.mapping" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\Mapping">
            <tag name="shopware_elastic_search.mapping" />
            <argument type="service" id="shopware_elastic_search.field_mapping" />
        </service>

        <service id="<?= $names->under_score_js ?>.synchronizer.synchronizer" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\Synchronizer">
            <tag name="shopware_elastic_search.synchronizer"/>
            <argument type="service" id="<?= $names->under_score_js ?>.data_indexer.data_indexer" />
            <argument type="service" id="dbal_connection" />
        </service>

        <service id="<?= $names->under_score_js ?>.settings.settings" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\Settings">
            <tag name="shopware_elastic_search.settings" />
        </service>

        <service
                id="shopware_search.<?= $names->under_score_js ?>.product_search_decorator"
                class="<?= $configuration->name; ?>\Components\SearchBundleES\Search"
                decorates="shopware_search.product_search"
                public="false"
        >
            <argument type="service" id="shopware_search.<?= $names->under_score_js ?>.product_search_decorator.inner" />
            <argument type="service" id="shopware_elastic_search.client" />
            <argument type="service" id="shopware_elastic_search.index_factory" />
        </service>
<?php } ?>

    </services>
</container>
