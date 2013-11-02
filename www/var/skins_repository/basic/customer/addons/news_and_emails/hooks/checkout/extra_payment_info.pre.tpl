
{if $page_mailing_lists}

	{include file="common_templates/subheader.tpl" title=$lang.text_signup_for_subscriptions}

	<div class="subscription-container" id="subsciption_{$tab_id}">
		<input type="hidden" name="mailing_lists" value="" />
		{foreach from=$page_mailing_lists item=list}
			<div class="select-field {if !$list.show_on_checkout}hidden{/if}">
				<label><input type="checkbox" name="mailing_lists[]" value="{$list.list_id}" {if $user_mailing_lists[$list.list_id]}checked="checked"{/if} class="checkbox cm-news-subscribe" />{$list.object}</label>
			</div>
		{/foreach}
	<!--subsciption_{$tab_id}--></div>
{/if}