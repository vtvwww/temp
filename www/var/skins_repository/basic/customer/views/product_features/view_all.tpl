{if $view_all_filter}
{assign var="filter_qstring" value=$smarty.request.q|fn_query_remove:"result_ids":"filter_id":"features_hash"}
{split data=$view_all_filter size="4" assign="splitted_filter" preverse_keys=true}
<table cellpadding="5" cellspacing="0" border="0" width="100%" class="view-all">
{foreach from=$splitted_filter item="group"}
<tr valign="top">
	{foreach from=$group item="ranges" key="index"}
	<td class="center">
		<div>
			{if $ranges}
				{include file="common_templates/subheader.tpl" title=$index}
				<ul class="arrows-list">
				{foreach from=$ranges item="range"}
					{assign var="_features_hash" value=$params.features_hash|fn_add_range_to_url_hash:$range}
					<li><a href="{if $range.feature_type == "E"}{"product_features.view?variant_id=`$range.range_id``$cur_features_hash`"|fn_url}{else}{"`$filter_qstring`&features_hash=`$_features_hash`"|fn_url}{/if}">{$range.range_name|fn_text_placeholders}</a></li>
				{/foreach}
			</ul>
			{else}&nbsp;{/if}
		</div>
	</td>
	{/foreach}
</tr>
{/foreach}
</table>
{/if}