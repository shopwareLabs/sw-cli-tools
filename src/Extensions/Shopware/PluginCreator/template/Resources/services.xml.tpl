<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <!-- @todo Add your services here -->
<?php if ($configuration->hasCommands) { ?>
        <service id="" class="<?= $configuration->name; ?>\Commands\<?= $names->camelCaseModel; ?>">
            <tag name="console.command" />
        </service>
<?php } else { ?>
        <!--
        <service id="your_plugin.example_service" class="YourPlugin\Example">
            <argument id="another_service" />
        </service>
        -->
<?php } ?>
    </services>
</container>