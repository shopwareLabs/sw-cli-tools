<?xml version="1.0" encoding="utf-8"?>
<menu xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/shopware/shopware/5.2/engine/Shopware/Components/Plugin/schema/menu.xsd">
    <entries>
        <entry>
            <name><?= $configuration->name ?></name>
            <label lang="en"><?= $configuration->name ?></label>
            <label lang="de"><?= $configuration->name ?></label>
            <controller><?= $configuration->name ?></controller>
            <action>index</action>
            <class>sprite-application-block</class>
            <parent identifiedBy="controller">Marketing</parent>
        </entry>
    </entries>
</menu>