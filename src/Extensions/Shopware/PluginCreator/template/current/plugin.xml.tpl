<?= '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL; ?>
<plugin xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="../../../engine/Shopware/Components/Plugin/schema/plugin.xsd">
    <label lang="de"><?= $configuration->name; ?></label>
    <label lang="en"><?= $configuration->name; ?></label>
    <version>1.0.0</version>
    <copyright><?= $configuration->pluginConfig['copyright']; ?></copyright>
    <license><?= $configuration->pluginConfig['license']; ?></license>
    <link><?= $configuration->pluginConfig['link']; ?></link>
    <author><?= $configuration->pluginConfig['author']; ?></author>

    <compatibility minVersion="5.2.0" />

    <changelog version="1.0.0">
        <changes lang="de">Erste Veröffentlichung</changes>
        <changes lang="en">Initial Release</changes>
    </changelog>
</plugin>