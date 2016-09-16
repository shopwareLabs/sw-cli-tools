<?= $configuration->licenseHeader; ?>

{extends file="parent:frontend/listing/actions/action-sorting.tpl"}

{block name='frontend_listing_actions_sort_values'}
    <option value="<?= $names->under_score_js ?>_random"{if $sSort eq "<?= $names->under_score_js ?>_random"} selected="selected"{/if}>Example random sorting</option>
{/block}