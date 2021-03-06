{if !$active_tab}
	{assign var="active_tab" value=$smarty.request.selected_section}
{/if}

{if $navigation.tabs}

{assign var="empty_tab_ids" value=$content|empty_tabs}
{assign var="_tabs" value=false}

{script src="js/tabs.js"}
<div class="tabs cm-j-tabs{if $track} cm-track{/if} clearfix">
	<ul {if $tabs_section}id="tabs_{$tabs_section}"{/if}>
	{foreach from=$navigation.tabs item=tab key=key name=tabs}
		{if ((!$tabs_section && !$tab.section) || ($tabs_section == $tab.section)) && !$key|in_array:$empty_tab_ids}
		{if !$active_tab}
			{assign var="active_tab" value=$key}
		{/if}
		{assign var="_tabs" value=true}
		<li id="{$key}" class="{if $tab.js}cm-js{elseif $tab.ajax}cm-js cm-ajax{/if}{if $key == $active_tab} cm-active{/if}"><a{if $tab.href} href="{$tab.href|fn_url}"{/if}>{$tab.title}</a></li>
		{/if}
	{/foreach}
	</ul>
</div>

{if $_tabs}
<div class="cm-tabs-content clearfix" id="tabs_content">
	{$content}
</div>
{/if}

{if $onclick}
<script type="text/javascript">
//<![CDATA[
	var hndl = {$ldelim}
		'tabs_{$tabs_section}': {$onclick}
	{$rdelim}
//]]>
</script>
{/if}
{else}
	{$content}
{/if}