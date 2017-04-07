<?= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; ?>
<?php if ($configuration->isLegacyPlugin) { ?>
<phpunit bootstrap="../../../../../../tests/Shopware/TestHelper.php">
<?php } else { ?>
<phpunit bootstrap="../../../tests/Functional/bootstrap.php">
<?php } ?>
<testsuite name="<?= $configuration->name; ?> Test Suite">
    <directory>tests</directory>
</testsuite>
</phpunit>
