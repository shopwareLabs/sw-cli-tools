<?= $configuration->licenseHeader; ?>

{extends file='frontend/index/index.tpl'}

{block name="frontend_index_content"}
    <h1>Hello world, this plugin is called <?= $configuration->name; ?> :)</h1>
{/block}