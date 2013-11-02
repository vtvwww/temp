{* $Id: top_quick_links.tpl 11154 2010-11-09 07:48:03Z 2tl $ *}
{hook name="index:top_links"}
	{foreach from=$quick_links item="link"}
		<a href="{$link.param|fn_url}">{$link.descr}</a>&nbsp;&nbsp;&nbsp;
	{/foreach}
{/hook}
