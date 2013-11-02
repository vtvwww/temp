<div id="storefront_settings">

{include file="common_templates/subheader.tpl" title=$lang.tw_manage_storefront_settings}

<fieldset>

	<div class="form-field">
		<label for="elm_tw_use_mobile_frontend">{$lang.use_mobile_frontend}:</label>
		<select id="elm_tw_use_mobile_frontend" name="tw_settings[use_mobile_frontend]">
			<option	value="never" {if $addons.twigmo.use_mobile_frontend == "never"}selected="selected"{/if}>{$lang.tw_never}</option>
			<option	value="phone" {if $addons.twigmo.use_mobile_frontend == "phone"}selected="selected"{/if}>{$lang.tw_phone}</option>
			<option	value="tablet" {if $addons.twigmo.use_mobile_frontend == "tablet"}selected="selected"{/if}>{$lang.tw_tablet}</option>
			<option	value="both_tablet_and_phone" {if $addons.twigmo.use_mobile_frontend == "both_tablet_and_phone"  || !$addons.twigmo.use_mobile_frontend}selected="selected"{/if}>{$lang.tw_both_tablet_and_phone}</option>
		</select>
	</div>
	<div class="form-field">
		<label class="cm-required" for="elm_tw_company_name">{$lang.company_name}:</label>
		
		<input type="text" id="elm_tw_company_name" name="tw_settings[companyName]"  value="{if $addons.twigmo.companyName}{$addons.twigmo.companyName}{else}{$settings.Company.company_name}{/if}" class="input-text-large" size="60" />
	</div>

	<div class="form-field">
		<label for="elm_tw_display_company_name">{$lang.display_company_name}:</label>
		<input type="hidden" name="tw_settings[displayCompanyName]" value="N" />
		<input type="checkbox" class="checkbox" id="elm_tw_display_company_name" name="tw_settings[displayCompanyName]" value="Y" {if !$addons.twigmo.displayCompanyName || $addons.twigmo.displayCompanyName == "Y"}checked="checked"{/if} />
	</div>

	<div class="form-field">
		<label for="elm_tw_disable_anonymous_checkout">{$lang.disable_anonymous_checkout}:</label>
		<input type="hidden" name="tw_settings[anonymousCheckoutDisabled]" value="N" />
		<input type="checkbox" class="checkbox" id="elm_tw_disable_anonymous_checkout" name="tw_settings[anonymousCheckoutDisabled]" value="Y" {if $addons.twigmo.anonymousCheckoutDisabled == "Y" || ( !$addons.twigmo.anonymousCheckoutDisabled && $settings.General.disable_anonymous_checkout == "Y")}checked="checked"{/if} />
	</div>

	<div class="form-field">
		<label for="elm_tw_home_page_content">{$lang.home_page_content}:</label>
		<select id="elm_tw_home_page_content" name="tw_settings[home_page_content]">
			<option	value="home_page_blocks" {if $addons.twigmo.home_page_content == "home_page_blocks"}selected="selected"{/if}>- {$lang.home_page_blocks} -</option>
			<option	value="tw_home_page_blocks" {if $addons.twigmo.home_page_content == "tw_home_page_blocks"}selected="selected"{/if}>- {$lang.tw_home_page_blocks} -</option>
			<option	value="random_products" {if $addons.twigmo.home_page_content == "random_products"}selected="selected"{/if}>- {$lang.random_products} -</option>
			{foreach from=0|fn_get_plain_categories_tree:false item="cat"}
			{if $cat.status == "A"}
				<option	value="{$cat.category_id}" {if $addons.twigmo.home_page_content == $cat.category_id}selected="selected"{/if}>{$cat.category|escape|indent:$cat.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
			{/if}
			{/foreach}
		</select>
		{include file="buttons/button.tpl" but_text=$lang.edit_home_page_blocks but_href="block_manager.manage&selected_location=`$locations_info.index`" but_id="elm_edit_home_page_blocks" but_role="link" but_meta="hidden"}
		{include file="buttons/button.tpl" but_text=$lang.edit_tw_home_page_blocks but_href="block_manager.manage&selected_location=`$locations_info.twigmo`" but_id="elm_edit_tw_home_page_blocks" but_role="link" but_meta="hidden"}
	</div>
	
	<script type="text/javascript">
	//<![CDATA[
	{literal}
	function fn_tw_show_block_link() {
		value = $('#elm_tw_home_page_content option:selected').val();
		if ((value == 'home_page_blocks') || (value == 'tw_home_page_blocks')) {			
			if (value == 'home_page_blocks') {
				$('#elm_edit_home_page_blocks').show();
				$('#elm_edit_tw_home_page_blocks').hide();
			} else {
				$('#elm_edit_tw_home_page_blocks').show();
				$('#elm_edit_home_page_blocks').hide();
			}
		} else {
			$('#elm_edit_home_page_blocks').hide();
			$('#elm_edit_tw_home_page_blocks').hide();
		}
		
		return true;
	}
	
	$("#elm_tw_home_page_content").change(function () {
		fn_tw_show_block_link();
	}).change();
	{/literal}
	//]]>
	</script>

	<div class="form-field">
		<label class="cm-required" for="elm_tw_logo_url">{$lang.logo_link}:</label>
		<input type="text" id="elm_tw_logo_url" name="tw_settings[logoURL]" value="{if $addons.twigmo.logoURL}{$addons.twigmo.logoURL}{else}http://{$config.http_host}{$images_dir|replace:"admin":"customer"}/{$manifest.Customer_logo.filename}{/if}" class="input-text-large" size="60">
	</div>

	<div class="form-field">
		<label for="elm_tw_apply_custom_css">{$lang.apply_custom_css}:</label>
		<input type="hidden" name="tw_settings[apply_custom_css]" value="N" />
		<input type="checkbox" class="checkbox" id="elm_tw_apply_custom_css" name="tw_settings[apply_custom_css]" value="Y" {if $addons.twigmo.apply_custom_css == "Y"}checked="checked"{/if} />
		{include file="buttons/button.tpl" but_text=$lang.edit_css but_href="twigmo.post&action=edit_css" but_role="link"}
	</div>

</fieldset>
<!--storefront_settings--></div>
