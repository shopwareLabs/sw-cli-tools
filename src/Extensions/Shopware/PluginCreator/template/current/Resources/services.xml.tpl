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

<?php if ($configuration->hasBackend || $configuration->hasFrontend || $configuration->hasWidget || $configuration->hasApi) { ?>
        <service id="<?= $names->under_score_js ?>.subscriber.controller_path" class="<?= $configuration->name; ?>\Subscriber\ControllerPath">
            <argument type="service" id="service_container" />
            <tag name="shopware.event_subscriber" />
        </service>
<?php } ?>

<?php if ($configuration->hasFilter) { ?>
        <service id="<?= $names->under_score_js ?>.subscriber.search_bundle" class="<?= $configuration->name; ?>\Subscriber\SearchBundle">
            <tag name="shopware.event_subscriber" />
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


    </services>
</container>