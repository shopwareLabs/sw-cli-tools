{
    "label": {
        "de": "<?= $configuration->name; ?>",
        "en": "<?= $configuration->name; ?>"
    },
    "copyright": "<?= $configuration->pluginConfig['copyright']; ?>",
    "license": "<?= $configuration->pluginConfig['license']; ?>",
    "link": "<?= $configuration->pluginConfig['link']; ?>",
    "author": "<?= $configuration->pluginConfig['author']; ?>",

    "currentVersion": "1.0.0",

    "changelog": {
        "de": {
            "1.0.0": "Erstveröffentlichung"
        },
        "en": {
            "1.0.0": "First release"
        }
    },

    "compatibility": {
        "minimumVersion": "<?= $configuration->pluginConfig['minimumVersion']; ?>",
        "maximumVersion": null,
        "blacklist": [
        ]
    }
}
