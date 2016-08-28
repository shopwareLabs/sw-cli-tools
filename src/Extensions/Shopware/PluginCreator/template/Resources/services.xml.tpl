<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <!-- @todo Add your services here -->
        <service id="<?= $names->under_score_js ?>.subscriber.frontend" class="<?= $configuration->name; ?>\Subscriber\Frontend">
            <tag name="shopware.event_subscriber" />
        </service>
<?php if ($configuration->hasCommands) { ?>

        <service id="<?= $names->under_score_js ?>.commands.<?= $names->under_score_model ?>" class="<?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>">
            <tag name="console.command" />
        </service>
<?php } ?>
    </services>
</container>