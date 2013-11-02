{* $Id: subscribe.tpl 12928 2011-07-12 11:21:02Z akulakov $ *}

{if $items}
<form action="{""|fn_url}" method="post" name="subscribe_form" class="subscribe-block">
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<p>{$lang.text_signup_for_subscriptions}</p>
{foreach from=$items item=list name="mailing_lists"}
	<div class="select-field">
		<label for="mailing_list_{$block.block_id}{$list.list_id}">
			<input id="mailing_list_{$block.block_id}{$list.list_id}" type="checkbox" class="checkbox" name="mailing_lists[]" value="{$list.list_id}" />{$list.object}
		</label>
	</div>
{/foreach}
<div class="select-field">
	<select name="newsletter_format" id="newsletter_format{$block.block_id}">
		<option value="{$smarty.const.NEWSLETTER_FORMAT_TXT}">{$lang.txt_format}</option>
		<option value="{$smarty.const.NEWSLETTER_FORMAT_HTML}">{$lang.html_format}</option>
	</select>
</div>
{strip}
<div class="subscribe_form form-field">
	<label for="subscr_email{$block.block_id}" class="cm-required cm-email hidden">{$lang.email}</label>
	<input type="text" name="subscribe_email" id="subscr_email{$block.block_id}" size="20" value="{$lang.enter_email|escape:html}" class="input-text cm-hint subscribe-email" />
	{include file="buttons/go.tpl" but_name="newsletters.add_subscriber" alt=$lang.go}
</div>
{/strip}
</form>
{/if}
