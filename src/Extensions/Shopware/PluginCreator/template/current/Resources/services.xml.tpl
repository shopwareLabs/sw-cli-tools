<?xml version="1.0" ?>

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

        <service id="<?= $names->under_score_js; ?>.random_sorting.random_sorting" class="<?= $configuration->name; ?>\Components\SearchBundleDBAL\Sorting\RandomSortingHandler">
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
        <service id="<?= $names->under_score_js ?>.blog_search.blog_provider" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\BlogProvider">
            <argument id="dbal_connection" type="service" />
        </service>

        <service id="<?= $names->under_score_js ?>.data_indexer.blog" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\BlogDataIndexer">
            <tag name="shopware_elastic_search.data_indexer" />
            <argument id="dbal_connection" type="service"/>
            <argument id="shopware_elastic_search.client" type="service"/>
            <argument id="<?= $names->under_score_js ?>.blog_search.blog_provider" type="service"/>
        </service>

        <service id="<?= $names->under_score_js ?>.mapping.blog" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\BlogMapping">
            <tag name="shopware_elastic_search.mapping" />
            <argument type="service" id="shopware_elastic_search.field_mapping" />
        </service>

        <service id="<?= $names->under_score_js ?>.synchronizer.blog" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\BlogSynchronizer">
            <tag name="shopware_elastic_search.synchronizer"/>
            <argument type="service" id="<?= $names->under_score_js ?>.data_indexer.blog" />
            <argument type="service" id="dbal_connection" />
        </service>

        <service id="<?= $names->under_score_js ?>.settings.blog" class="<?= $configuration->name; ?>\Components\ESIndexingBundle\BlogSettings">
            <tag name="shopware_elastic_search.settings" />
        </service>

        <service id="<?= $names->under_score_js ?>.subscriber.orm_backlog" class="<?= $configuration->name; ?>\Subscriber\ORMBacklogSubscriber">
            <tag name="doctrine.event_subscriber" />
            <argument id="service_container" type="service"/>
        </service>

        <service
                id="shopware_search.<?= $names->under_score_js ?>.product_search_decorator"
                class="<?= $configuration->name; ?>\Components\SearchBundleES\BlogSearch"
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